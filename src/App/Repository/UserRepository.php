<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Doctrine\ORM\NoResultException;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * User repository
 */
class UserRepository extends AbstractRepository implements UserProviderInterface
{

    private $userClass = 'App\Entity\Users';

    private $passwordEncoder;

    private $passwordStrengthValidator;

    public function __construct(\Silex\Application $app){
        $this->passwordEncoder = $app['security.encoder_factory']->getEncoder(new $this->userClass());
        parent::__construct($app['orm.em'], $app['orm.em']->getClassMetadata($this->userClass));
    }


    public function save($item)
    {
        $this->checkInstance($item);

        if(is_null($item->getId())){
            $item->setCreatedAt(new \DateTime());
        }

        // If the password was changed, re-encrypt it.
        if (strlen($item->getPassword()) != 88) {
            $item->setSalt(uniqid(mt_rand()));
            $item->setPassword($this->passwordEncoder->encodePassword($item->getPassword(), $item->getSalt()));
        }


        if($item->getImage() instanceof UploadedFile){
            $item->setImage($this->processImage($item->getImage()));
        }else{
            $item->setImage('http://www.gravatar.com/avatar/'.md5(trim($item->getEmail())));
        }

        $this->_em->persist($item);
        $this->_em->flush();
    }

    protected function processImage(UploadedFile $uploaded_file)
    {
        $path = UPLOADS_PATH .'/avatars/';
        //getClientOriginalName() => Returns the original file name.

        $file_name = sha1(uniqid(mt_rand(), true)).'.'.$uploaded_file->guessExtension();

        $uploaded_file->move($path, $file_name);

        return $file_name;
    }

    // ----- UserProviderInterface -----
    /**
     * Loads the user for the given username or email address.
     *
     * Required by UserProviderInterface.
     *
     * @param string $username The username
     * @return UserInterface
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        //$q = $this->createQueryBuilder('u')
        //    ->where('u.username = :username OR u.email = :email')
        //    ->setParameter('username', $username)
        //    ->setParameter('email', $username)
        //    ->getQuery();

        $q = $this
            ->createQueryBuilder('u')
            ->select('u, g')
            ->leftJoin('u.groups', 'g')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery();

        try {
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
        }
        return $user;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     * @return UserInterface
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        $this->checkInstance($user);

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param string $class
     * @return Boolean
     */
    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }

    // ----- End UserProviderInterface -----

    /**
     * Reconstitute a User object from stored data.
     *
     * @param array $data
     * @return User
     * @throws \RuntimeException if database schema is out of date.
     */
    protected function hydrateUser(array $data)
    {
        $userClass = $this->getEntityName();

        /** @var User $user */
        $user = new $userClass();

        $user->setId($data['id']);
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setSalt($data['salt']);
        $user->setPassword($data['password']);
        if ($roles = explode(',', $data['roles'])) {
            $user->setRoles($roles);
        }
        $user->setCreatedAt($data['created_at']);
        $user->setImage($data['image']);
        $user->setIsActive($data['is_active']);
        $user->setConfirmationToken($data['confirmationToken']);
//        $user->setTimePasswordResetRequested($data['timePasswordResetRequested']);

        return $user;
    }

    /**
     * Encode a plain text password for a given user. Hashes the password with the given user's salt.
     *
     * @param User $user
     * @param string $password A plain text password.
     * @return string An encoded password.
     */
    public function encodeUserPassword(User $user, $password)
    {
        return $this->passwordEncoder->encodePassword($password, $user->getSalt());
    }

    /**
     * Encode a plain text password and set it on the given User object.
     *
     * @param User $user
     * @param string $password A plain text password.
     */
    public function setUserPassword(User $user, $password)
    {
        $user->setPassword($this->encodeUserPassword($user, $password));
    }


    /**
     * Test whether a plain text password is strong enough.
     *
     * Note that controllers must call this explicitly,
     * it's NOT called automatically when setting a password or validating a user.
     *
     * This is just a proxy for the Callable set by setPasswordStrengthValidator().
     * If no password strength validator Callable is explicitly set,
     * by default the only requirement is that the password not be empty.
     *
     * @param User $user
     * @param $password
     * @return string|null An error message if validation fails, null if validation succeeds.
     */
    public function validatePasswordStrength(User $user, $password)
    {
        return call_user_func($this->getPasswordStrengthValidator(), $user, $password);
    }

    /**
     * @return callable
     */
    public function getPasswordStrengthValidator()
    {
        if (!is_callable($this->passwordStrengthValidator)) {
            return function(User $user, $password) {
                if (empty($password)) {
                    return 'Password cannot be empty.';
                }

                return null;
            };
        }

        return $this->passwordStrengthValidator;
    }

    /**
     * Specify a callable to test whether a given password is strong enough.
     *
     * Must take a User instance and a password string as arguments,
     * and return an error string on failure or null on success.
     *
     * @param Callable $callable
     * @throws \InvalidArgumentException
     */
    public function setPasswordStrengthValidator($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException('Password strength validator must be Callable.');
        }

        $this->passwordStrengthValidator = $callable;
    }

    /**
     * Test whether a given plain text password matches a given User's encoded password.
     *
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function checkUserPassword(User $user, $password)
    {
        return $user->getPassword() === $this->encodeUserPassword($user, $password);
    }

    /**
     * Get a User instance for the currently logged in User, if any.
     *
     * @return UserInterface|null
     */
    public function getCurrentUser()
    {
        if ($this->isLoggedIn()) {
            return $this->app['security.token_storage']->getToken()->getUser();
        }

        return null;
    }

    /**
     * Test whether the current user is authenticated.
     *
     * @return boolean
     */
    function isLoggedIn()
    {
        $token = $this->app['security.token_storage']->getToken();
        if (null === $token) {
            return false;
        }

        $fully      = $this->app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY');
        $remembered = $this->app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_REMEMBERED');

        return ($fully || $remembered);
    }

    /**
     * Log in as the given user.
     *
     * Sets the security token for the current request so it will be logged in as the given user.
     *
     * @param User $user
     */
    public function loginAsUser(User $user)
    {
        if (null !== ($current_token = $this->app['security.token_storage']->getToken())) {
            $providerKey = method_exists($current_token, 'getProviderKey') ? $current_token->getProviderKey() : $current_token->getKey();
            $token = new UsernamePasswordToken($user, null, $providerKey);
            $this->app['security.token_storage']->setToken($token);
        }
    }
}