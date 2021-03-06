<?php
/**
 * DataModel interface.
 *
 */
namespace JamesJara\X7Cloud\Data;

/**
 * IDataModel interface.
 */
interface IDataModel
{

    /**
     * Return the specific metadata
     *
     * @todo create class type
     * @param int $type            
     * @return array
     */
    public function metadata($type);

    /**
     * Return an array with all table results
     *
     * @param array $columns            
     * @return array
     */
    public function getAll($columns);

    /**
     * Return an array with change state
     *
     * @param array $columns            
     * @return array
     */
    public function change($changes);

    /**
     * Return an array with remove state
     *
     * @param array $ids            
     * @return array
     */
    public function remove($ids);

    /**
     * Return an array with insert state
     *
     * @param array $data            
     * @return array
     */
    public function insert($data);
}
