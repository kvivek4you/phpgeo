<?php
/**
 * Coordinate Factory
 *
 * @author   Marcus Jaschen <mjaschen@gmail.com>
 * @license  https://opensource.org/licenses/GPL-3.0 GPL
 * @link     https://github.com/mjaschen/phpgeo
 */

namespace Location\Factory;

use Location\Coordinate;
use Location\Ellipsoid;

/**
 * Coordinate Factory
 *
 * @author   Marcus Jaschen <mjaschen@gmail.com>
 * @license  https://opensource.org/licenses/GPL-3.0 GPL
 * @link     https://github.com/mjaschen/phpgeo
 */
class CoordinateFactory implements GeometryFactoryInterface
{
    /**
     * Creates a Coordinate instance from the given string.
     *
     * The string is parsed by a regular expression for a known
     * format of geographical coordinates.
     *
     * The simpler formats are tried first, before the string is
     * checked for more complex coordinate represenations.
     *
     * @param string $string formatted geographical coordinate
     * @param \Location\Ellipsoid $ellipsoid
     *
     * @return \Location\Coordinate
     */
    public static function fromString($string, Ellipsoid $ellipsoid = null)
    {
        // The most simple format: decimal degrees without cardinal letters,
        // e. g. "52.5, 13.5" or "53.25732 14.24984"
        if (preg_match('/(-?\d{1,2}\.?\d*)[, ]\s*(-?\d{1,3}\.?\d*)/', $string, $match)) {
            return new Coordinate($match[1], $match[2], $ellipsoid);
        }

        // Decimal degrees with cardinal letters, e. g. "N52.5, E13.5" or
        // "40.2S, 135.3485W"
        if (preg_match('/([NS]?\s*)(\d{1,2}\.?\d*)(\s*[NS]?)[, ]\s*([EW]?\s*)(\d{1,2}\.?\d*)(\s*[EW]?)/i', $string, $match)) {
            $latitude = $match[2];
            if (trim(strtoupper($match[1])) === 'S' || trim(strtoupper($match[3])) === 'S') {
                $latitude = - $latitude;
            }
            $longitude = $match[5];
            if (trim(strtoupper($match[4])) === 'W' || trim(strtoupper($match[6])) === 'W') {
                $longitude = - $longitude;
            }

            return new Coordinate($latitude, $longitude, $ellipsoid);
        }

        throw new \InvalidArgumentException("Format of coordinates was not recognized");
    }
}