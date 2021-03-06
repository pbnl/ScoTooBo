<?php

namespace App\Model\Services;

use App\Entity\LDAP\PbnlMailAlias;
use App\Model\LdapComponent\PbnlLdapEntityManager;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
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
     * @param LoggerInterface $logger
     * @param PbnlLdapEntityManager $ldapEntityManager
     * @param ValidatorInterface $validator
     */
    public function __construct(LoggerInterface $logger, PbnlLdapEntityManager $ldapEntityManager, ValidatorInterface $validator)
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
            throw new GroupNotFoundException("We cant find the mailAlias " . $mail);
        }
        return $mailAlias[0];
    }

    public function update(PbnlMailAlias $mailAlias)
    {
        if (!$this->doesMailAliasExist($mailAlias)) {
            throw new MailAliasDoesNotExistException("The user " . $mailAlias->getMail() . " does not exist.");
        }
        $this->ldapEntityManager->persist($mailAlias);
    }

    private function doesMailAliasExist($mailAlias)
    {
        $mailAlias = $this->mailAliasLdapRepository->findByMail($mailAlias);
        if ($mailAlias == []) {
            return false;
        }
        return true;
    }

    public function add(PbnlMailAlias $mailAlias)
    {
        if (!$this->doesMailAliasExist($mailAlias)) {
            $this->ldapEntityManager->persist($mailAlias);
        } else {
            throw new DatabaseObjectAllreadytExistsException("The MailAlias " . $mailAlias->getMail() . " does allready exist.");
        }
    }

    public function remove(PbnlMailAlias $mailAlias)
    {
        if ($this->doesMailAliasExist($mailAlias)) {
            $this->ldapEntityManager->delete($mailAlias);
        } else {
            throw new DatabaseObjectDoesNotExistsException("The MailAlias " . $mailAlias->getMail() . " does not exist.");
        }
    }
}
