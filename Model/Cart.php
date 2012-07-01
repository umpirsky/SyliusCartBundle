<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Model;

/**
 * Model for carts.
 * All driver entities and documents should extend this class or implement
 * proper interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class Cart implements CartInterface
{
    /**
     * Id.
     *
     * @var integer
     */
    protected $id;

    /**
     * Items in cart.
     *
     * @var array
     */
    protected $items;

    /**
     * Total items count.
     *
     * @var integer
     */
    protected $totalItems;

    /**
     * Locked.
     *
     * @var Boolean
     */
    protected $locked;

    /**
     * Expiration time.
     *
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->items = array();
        $this->totalItems = 0;
        $this->locked = false;
        $this->incrementExpiresAt();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementTotalItems($amount = 1)
    {
        $this->totalItems += $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalItems($totalItems)
    {
        $this->totalItems = $totalItems;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return 0 === $this->countItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * {@inheritdoc}
     */
    public function countItems()
    {
        return count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(ItemInterface $item)
    {
        foreach ($this->items as $existingItem) {
            /** @var $existingItem ItemInterface */
            if ($existingItem->equals($item)) {
                $existingItem->incrementQuantity($item->getQuantity());

                return;
            }
        }

        $item->setCart($this);
        $this->items[] = $item;
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(ItemInterface $item)
    {
        $key = $this->searchItem($item);
        if (false !== $key) {
            unset($this->items[$key]);
            $item->setCart(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem(ItemInterface $item)
    {
        return false !== $this->searchItem($item);
    }

    /**
     * @param ItemInterface $item
     *
     * @return boolean|integer
     */
    public function searchItem(ItemInterface $item)
    {
        return array_search($item, $this->items, true);
    }

    /**
     * {@inheritdoc}
     */
    public function clearItems()
    {
        $this->items = array();
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired()
    {
        return $this->getExpiresAt() < new \DateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiresAt(\DateTime $expiresAt = null)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementExpiresAt()
    {
        $expiresAt = new \DateTime();
        $expiresAt->add(new \DateInterval('PT3H'));

        $this->expiresAt = $expiresAt;
    }
}
