<?php
namespace App\Security;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Security\DomainAccessDecisionManagerInterface;

class MyVoter implements VoterInterface
{
    const ROLE_ALLOWED_ON_DOMAIN = 'ALLOWED_ON_DOMAIN';

    private $decisionManager;

    public function __construct(DomainAccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    public function supportsAttribute($attribute)
    {
        return self::ROLE_ALLOWED_ON_DOMAIN === $attribute;
    }

    public function supportsClass($class)
    {
        return true;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $result = self::ACCESS_ABSTAIN;

        if (!($object instanceof Request)) {
            return $result;
        }

        $user = $token->getUser();
        if (!($user instanceof UserInterface)) {
            return $result;
        }

        /* @var $object Request */

        foreach ($attributes as $attribute) {
            // these attributes come from the access control rules in the security configuration
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            $host = $object->getHost();
            $user = $token->getUser();

            if ($this->decisionManager->decide($user, $host)) {
                $result = self::ACCESS_GRANTED;
            }
            else {
                $result = self::ACCESS_DENIED;
            }
        }

        return $result;
    }
}
