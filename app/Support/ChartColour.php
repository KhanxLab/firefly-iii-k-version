<?php

/**
 * ChartColour.php
 * Copyright (c) 2019 james@firefly-iii.org
 *
 * This file is part of Firefly III (https://github.com/firefly-iii).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace FireflyIII\Support;

/**
 * Class ChartColour.
 */
class ChartColour
{
    /**
     * @var array
     */
    public static $colours = [
        [30, 101, 129], // accent
        [232, 163, 61], // amber
        [15, 118, 110], // teal
        [194, 65, 107], // rose
        [79, 70, 229], // indigo
        [21, 128, 61], // green
        [194, 65, 12], // orange
        [100, 116, 139], // slate
        [14, 116, 144], // cyan
        [126, 34, 206], // purple
        [101, 163, 13], // lime
        [180, 83, 9], // brown
        [37, 99, 235], // blue
        [219, 39, 119], // pink
        [71, 85, 105], // dark slate
        [5, 150, 105], // emerald
        [161, 98, 7], // ochre
    ];

    public static function getColour(int $index): string
    {
        $index %= count(self::$colours);
        $row = self::$colours[$index];

        return sprintf('rgba(%d, %d, %d, 0.85)', $row[0], $row[1], $row[2]);
    }
}
