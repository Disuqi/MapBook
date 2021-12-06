<?php
Interface Repo{
    function getAll();
    function getObject($pk);
    function getAttribute($attribute, $pk);
    function objectExists($pk);
    function attributeExists($attribute, $value);
    function addObject($object);
    function deleteObject($pk);
    function getObjectsFromQuery($sqlQuery, $values = null);
    function executeQuery($sqlQuery, $values = null);
}