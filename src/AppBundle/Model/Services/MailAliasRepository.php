<?php

namespace AppBundle\Model\Services;

use AppBundle\Entity\LDAP\PbnlMailAlias;
use AppBundle\Entity\LDAP\PosixGroup;
use AppBundle\Model\Filter;
use AppBundle\Model\LdapComponent\PbnlLdapEntityManager;
use Monolog\Logger;
use Symfony\Component\Config\Tests\Util\Validator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MailAliasRepository
{
    /**
     * A reference to the LdapEntityService to work with the ldap
     *
     * @var PbnlLdapEntityManager
     */
    private $ldapEntityManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Validator
     */
    private $validator;


    private $mailAliasLdapRepository;



    /**
     * The ldapManager of the LDAPBundle
     *
     * @param Logger $logger
     * @param PbnlLdapEntityManager $ldapEntityManager
     * @param ValidatorInterface $validator
     */
    public function __construct(Logger $logger, PbnlLdapEntityManager $ldapEntityManager, ValidatorInterface $validator)
    {
        $this->ldapEntityManager = $ldapEntityManager;
        $this->logger = $logger;
        $this->validator = $validator;
        $this->mailAliasLdapRepository = $this->ldapEntityManager->getRepository(PbnlMailAlias::class);
    }


    /**
     * @return array
     */
    public function findAll()
    {
        return $this->mailAliasLdapRepository->findAll();
    }

    public function findByMail($mail)
    {
        $mailAlias = $this->mailAliasLdapRepository->findByMail($mail);
        if ($mailAlias == []) {
            throw new GroupNotFoundException("We cant find the mailAlias ".$mail);
        }
        return $mailAlias[0];
    }
}