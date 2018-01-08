<?php
/**
 * Part of the evias/nem-php package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under MIT License.
 *
 * This source file is subject to the MIT License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    evias/nem-php
 * @version    1.0.0
 * @author     Grégory Saive <greg@evias.be>
 * @author     Robin Pedersen (https://github.com/RobertoSnap)
 * @license    MIT License
 * @copyright  (c) 2017, Grégory Saive <greg@evias.be>
 * @link       http://github.com/evias/nem-php
 */
namespace NEM\Models;

class Mosaic
    extends Model
{
    /**
     * List of fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        "namespaceId",
        "name"
    ];

    /**
     * Mosaic DTO build a package with offsets `namespaceId` and
     * `name` as required by NIS for the mosaic identification.
     *
     * @return  array       Associative array with key `namespaceId` and `name` required for a NIS *compliable* mosaic identification.
     */
    public function toDTO($filterByKey = null)
    {
        return [
            "namespaceId" => $this->namespaceId,
            "name" => $this->name,
        ];
    }

    /**
     * Getter for the *fully qualified name* of the Mosaic.
     *
     * @return string
     */
    public function getFQN()
    {
        return sprintf("%s:%s", $this->namespaceId, $this->name);
    }
}
