<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 11.11.17
 * Time: 22:34
 */

namespace Tests\AppBundle\LdapComponent;


use AppBundle\Model\LdapComponent\LdapFilter;
use AppBundle\Model\LdapComponent\PbnlLdapEntityManager;
use AppBundle\Model\LdapComponent\Repositories\Repository;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    public function testFindAll()
    {
        $em = $this->createMock(PbnlLdapEntityManager::class);
        $em->expects($this->once())
            ->method("retrieve")
            ->with("AClass",[])
            ->willReturn([1,2]);

        $searchableAttributes = [];
        $repo = new Repository($em, "AClass", $searchableAttributes);

        $result = $repo->findAll();

        $this->assertEquals([1,2], $result);
    }

    public function testFindAllWithAttributes()
    {
        $em = $this->createMock(PbnlLdapEntityManager::class);
        $em->expects($this->once())
            ->method("retrieve")
            ->with("AClass",["attributes"=>["test"]])
            ->willReturn([1,2]);

        $searchableAttributes = [];
        $repo = new Repository($em, "AClass", $searchableAttributes);

        $result = $repo->findAll(["test"]);

        $this->assertEquals([1,2], $result);
    }

    public function testFindBy()
    {
        $options = array();
        $options['filter'] = new LdapFilter(array("attr" => "val"));

        $em = $this->createMock(PbnlLdapEntityManager::class);
        $em->expects($this->once())
            ->method("retrieve")
            ->with("AClass",$options)
            ->willReturn([1,2]);

        $searchableAttributes = [];
        $repo = new Repository($em, "AClass", $searchableAttributes);

        $result = $repo->findBy("attr", "val");

        $this->assertEquals([1,2], $result);
    }

    public function testFindByWithAttributes()
    {
        $options = array();
        $options['filter'] = new LdapFilter(array("attr" => "val"));
        $options['attributes'] = ["test"];

        $em = $this->createMock(PbnlLdapEntityManager::class);
        $em->expects($this->once())
            ->method("retrieve")
            ->with("AClass",$options)
            ->willReturn([1,2]);

        $searchableAttributes = [];
        $repo = new Repository($em, "AClass", $searchableAttributes);

        $result = $repo->findBy("attr", "val",["test"]);

        $this->assertEquals([1,2], $result);
    }

    public function testFindOneBy()
    {
        $options = array();
        $options['filter'] = new LdapFilter(array("attr" => "val"));

        $em = $this->createMock(PbnlLdapEntityManager::class);
        $em->expects($this->once())
            ->method("retrieve")
            ->with("AClass",$options)
            ->willReturn([1,2]);

        $searchableAttributes = [];
        $repo = new Repository($em, "AClass", $searchableAttributes);

        $result = $repo->findOneBy("attr", "val");

        $this->assertEquals(1, $result);
    }

    public function testFindOneByWithAttributes()
    {
        $options = array();
        $options['filter'] = new LdapFilter(array("attr" => "val"));
        $options['attributes'] = ["test"];

        $em = $this->createMock(PbnlLdapEntityManager::class);
        $em->expects($this->once())
            ->method("retrieve")
            ->with("AClass",$options)
            ->willReturn([1,2]);

        $searchableAttributes = [];
        $repo = new Repository($em, "AClass", $searchableAttributes);

        $result = $repo->findOneBy("attr", "val",["test"]);

        $this->assertEquals(1, $result);
    }

    public function testFindOneByEmptyResult()
    {
        $options = array();
        $options['filter'] = new LdapFilter(array("attr" => "val"));

        $em = $this->createMock(PbnlLdapEntityManager::class);
        $em->expects($this->once())
            ->method("retrieve")
            ->with("AClass",$options)
            ->willReturn([]);

        $searchableAttributes = [];
        $repo = new Repository($em, "AClass", $searchableAttributes);

        $result = $repo->findOneBy("attr", "val");

        $this->assertEquals([], $result);
    }
}