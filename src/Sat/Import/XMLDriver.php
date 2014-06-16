<?php


require "./loader.php";
$core = core::get_instance();

$importer = new sat_xml_importer();
$importer->run('import-fixture.xml');


class sat_xml_importer {

    /** @var tf_sat */
    private $module;
    /** @var sat_node_collection */
    private $collection;

    function run($file) {

        if (empty($file) || !file_exists($file)) throw new tf_exception('Empty file');

        $response = file_get_contents($file);
        $xmldoc = new SimpleXMLElement($response);

        $this->module       = core::module('sat');
        $this->collection   = $this->module->get_node_handle();

        $this->collection->remove_all_fast();

        foreach ($xmldoc->node as  $node)
            $this->import_node($node);

        $this->collection->sync_children_count();

    }

    function import_node($node, $p = null, $pitem = null) {

        printf("%s [%s] %s", $node->title, $p ? $p->title : '-', strings::nl());

        $pitem = $this->create_item(array('title' => (string)$node->title), $pitem);

        if (!empty($node->node))
            foreach ($node->node as $pnode) $this->import_node($pnode, $node, $pitem);

    }

    function create_item($data, $parent = null) {

        if ($parent) $data['pid'] = $parent->id;
        $id = $this->collection->create($data);

        return $this->collection->get_item_by_id($id);
    }

}

