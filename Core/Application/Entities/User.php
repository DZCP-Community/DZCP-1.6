<?php
/**
 * @projekt DZCP - deV!L`z ClanPortal 2.0
 * @url http://www.dzcp.de
 * @author Lucas Brucksch
 * @copyright (C) 2019 Codedesigns (MIT)
 * @package Application\Libraries
 * @version 1.0
 */

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Users
 * @package Models
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User {
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=11)
     */
    protected $id = 0;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, options={"default"=""})
     */
    protected $username = "";

    /**
     * @var string
     * @ORM\Column(type="string", length=256, options={"default"=""})
     */
    protected $password = "";

    /**
     * @return int
     */
    public final function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public final function getUsername(): string {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public final function setUsername(string $username) {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public final function getPassword(): string {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public final function setPassword(string $password) {
        $this->password = password_hash($password,PASSWORD_DEFAULT);
    }

    /**
     * @param string $password
     * @return bool
     */
    public final function checkPassword(string $password): bool {
       if(password_verify($password,$this->password)) {
           $this->password = password_needs_rehash($this->password, PASSWORD_DEFAULT) ?
               password_hash($password, PASSWORD_DEFAULT) : $this->password;
           return true;
       }

       return false;
    }
}