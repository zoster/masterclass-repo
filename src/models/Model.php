<?php
namespace App\Models;

interface Model
{
    /**
     * @param array $array
     */
    public function set(array $array);

    /**
     * @return string|false
     */
    public function errors();


    /**
     * @return bool
     */
    public function validate();

    /**
     * @return bool
     */
    public function create();
}
