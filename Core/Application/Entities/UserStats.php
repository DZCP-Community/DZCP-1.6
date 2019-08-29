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
 * Class UserStats
 * @package Models
 * @ORM\Entity
 * @ORM\Table(name="user_stats")
 */
class UserStats
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=11)
     */
    protected $id = 0;
}