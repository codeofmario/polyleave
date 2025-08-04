<?php
// app/Enums/RoleType.php
namespace App\Enums;

enum RoleType: string
{
    case User      = 'user';
    case Moderator = 'moderator';
    case Admin     = 'admin';
}
