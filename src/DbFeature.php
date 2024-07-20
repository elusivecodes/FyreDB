<?php
declare(strict_types=1);

namespace Fyre\DB;

/**
 * DbFeature
 */
enum DbFeature
{
    case DeleteAlias;
    case DeleteJoin;
    case DeleteMultipleTables;
    case DeleteUsing;
    case InsertReturning;
    case Replace;
    case UpdateFrom;
    case UpdateJoin;
    case UpdateMultipleTables;
}
