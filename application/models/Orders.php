<?php

/**
 * Data access wrapper for "orders" table.
 *
 * @author jim
 */
class Orders extends MY_Model {

    // constructor
    function __construct() {
        parent::__construct('orders', 'num');
    }

    // add an item to an order
    function add_item($num, $code) {
        $CI = &get_instance();
        // if an $num of order already exists, update the item in the order
        if ($CI->orderitems->exists($num, $code)) {
            $record = $CI->orderitems->get($num, $code);
            $record->quantity++;
            $CI->orderitems->update($record);
        } else {    // create the new order and update the item's quantity
            $record = $CI->orderitems->create();
            $record->order = $num;
            $record->item = $code;
            $record->quantity = 1;
            $CI->orderitems->add($record);
        }
    }

    // calculate the total for an order
    function total($num) {
        // change the total amount for an order by iterate over the items in an order
        $CI = &get_instance();
        $CI->load->model('orderitems');

        // get all the items in the order by calling some('code',$num)
        $items = $this->orderitems->some('order', $num);

        // used foreach loop to iterate all the item in items and add them up
        $result = 0.0;
        foreach ($items as $item) {
            $menuitem = $this->menu->get($item->item);
            $result = $result + $item->quantity * $menuitem->price;
        }

        // return total amount of items in the order
        return $result;
    }

    // retrieve the details for an order
    function details($num) {
        
    }

    // cancel an order
    function flush($num) {
        
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num) {
        $CI = &get_instance();
        $items = $CI->orderitems->group($num);
        $gotem = array();
        if(count($items) > 0)
        {
            foreach($items as $item)
            {
                $menu = $CI->menu->get($item->item);
                $gotem[$menu->category] = 1;
            }
        }
        
        return (isset($gotem['m']) && isset($gotem['d']) && isset($gotem['s']));
    }

}
