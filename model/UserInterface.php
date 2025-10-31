<?php

namespace model;

interface UserInterface
{
    function connect(array $tab): bool;
    function disconnect(): bool;
}