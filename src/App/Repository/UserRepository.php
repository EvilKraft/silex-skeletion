<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\ORM\NoResultException;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * User repository
 */
class UserRepository extends EntityRepository implements RepositoryInterface, UserProviderInterface
{

    private $passwordEncoder;

    public function __construct($em, ClassMetadata $class, PasswordEncoderInterface $passwordEncoder){

        $this->passwordEncoder = $passwordEncoder;

        parent::__construct($em, $class);
    }


    public function save(UserInterface $item)
    {
        $class = get_class($item);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

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
            $item->setImage('http://www.gravatar.com/avatar/'.md5(trim($item->getMail())));
        }

        $this->_em->persist($item);
        $this->_em->flush();
    }

    public function delete(UserInterface $item){
        $class = get_class($item);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        $this->_em->remove($item);
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
    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        $q = $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.mail = :mail')
            ->setParameter('username', $username)
            ->setParameter('mail', $username)
            ->getQuery();

        try {
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
        }
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        //return 'App\Entity\Users' === $class;

        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}