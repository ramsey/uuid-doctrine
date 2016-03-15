<?php
namespace Ramsey\Uuid\Doctrine;

/**
 * The Doctrine ORM defines \Doctrine\ORM\EntityManager::__construct as a
 * protected method, meaning PHPUnit can’t mock it. We need to extend the class
 * to make this method public for testing.
 */
class TestEntityManager extends \Doctrine\ORM\EntityManager
{
    public function __construct()
    {
    }
}
