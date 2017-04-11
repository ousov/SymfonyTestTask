<?php
namespace AppBundle\Repository;

/**
 * User_phonesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class User_phonesRepository extends \Doctrine\ORM\EntityRepository
{

    public function findPhoneById($id)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT user_phones.id 
                 FROM AppBundle:User_phones user_phones, AppBundle:Users users, AppBundle:User_profile user_profile 
                 WHERE users.id=user_profile.users 
                 AND user_phones.id=user_profile.user_phone
                 AND users.id=:id')
            ->setParameter('id', $id)
            ->getResult();
    }
}
