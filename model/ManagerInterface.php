<?php
namespace model;

use PDO;

interface ManagerInterface
{
    public function __construct(PDO $connect);
}