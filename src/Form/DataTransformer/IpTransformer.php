<?php
/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2017 Cerema
 * @license   CeCILL-B V1
 *
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * IpTransformer class.
 *
 * @category App\Form\DataTransformer
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class IpTransformer implements DataTransformerInterface
{
    /**
     * Transform Long into IP.
     *
     * @param mixed $ip
     *
     * @return string|null
     */
    public function transform($ip)
    {
        if (is_null($ip)) {
            return null;
        }

        return long2ip($ip);
    }

    /**
     * Transform IP to long.
     *
     * @param mixed $ip
     *
     * @return int
     */
    public function reverseTransform($ip)
    {
        if (empty($ip)) {
            return null;
        }

        return ip2long($ip);
    }
}
