<?php

require('../loader.php');

$core = core::get_instance();

/** @var model_collection $collection */
$collection = $core->model('test_images');

$collection->remove_all_fast();

$id = $collection->create(array('text' => 1));

$collection->clear(true)->configure(array('debug1' => true));
$item = $collection->load_only_id($id);

if (!$item) {
    test_print("ID-FAILED: ", $id);
}

test_assert(false === $collection->get_item_by_id(8888), 'null test #1');
test_assert(false === $collection->get_item_by_prop('name', 8888), 'null test #2');

test_assert($item instanceof model_item, 'LOAD_ONLY_ID');

$item =
    $collection->clear(true)
        ->set_where('id = %d', $id)
        ->load()
        ->get_item();

test_assert(sprintf('id = %d', $id) === $collection->get_where(), 'get-where');

test_assert(
    'SELECT p1.* FROM sat_test_images p1  WHERE id = 1  ORDER BY id DESC ;'
    === $collection->get_last_query(), 'sql check'
);

test_assert($item instanceof model_item, 'LOAD_ONLY_ID class');

if (!$item) return;

test_assert($item->id === $id, 'LOAD_ONLY_ID Id');

$item->text = 'text<h1>header</h1>';
$item->title = 'title<h1>header</h1>';

test_assert(function() use ($item) {

    $result = $item->save();
    return $result;

}, 'Save model');

/** @var model_item $item */
$item =
    $collection->clear(true)
        ->load_only_id($id);

test_assert($item instanceof model_item, 'LOAD_ONLY_ID class');

if (
    !test_assert($item->id === $id, '2-LOAD_ONLY_ID Id')
    || !test_assert($item->title === 'title<h1>header</h1>', '2-LOAD_ONLY_ID Title')
    || !test_assert($item->render('title') === 'title&lt;h1&gt;header&lt;/h1&gt;', '2-LOAD_ONLY_ID Render Title')
    || !test_assert(count($item->render(['text','title'])) === 2, 'Render filter []')
    || !test_assert($item->text === "text<h1>header</h1>", '2-LOAD_ONLY_ID Text')
) {
    test_print($item->title, $item->render('title'), $item->render());
}

$item->remove();

$item =
    $collection->clear(true)
        ->load_only_id($id);

test_assert(!$item, 'REMOVE-LOAD_ONLY_ID');

if (!test_assert(($count = $collection->clear()->count_sql()) === 0, 'REMOVE-COUNT')) {
    test_print($count);
}

test_except('core_exception', function(){
    core::get_instance()->model('NotExist');
}, 'exception-test');


//
// Alloc
//

$item = $collection->alloc();

test_assert($item instanceof model_item, 'alloc.1');

test_assert($item->is_allocated(), 'is-alloc');
test_assert($item->is_new(), 'is-new');

$item->title = "@title.2";
$item->save();

test_assert(($id = $item->get_id()) > 0, 'last-id');

test_assert(!$item->is_allocated(), 'is-alloc.2');
test_assert(!$item->is_new(), 'is-new.2');

// clone one
$item->set_id(null)->save();

$item = $collection->clear(true)->load_only_id($id);

test_assert($item, 'check item');
test_assert($collection->count() == 1, 'check item count');

if (!$item) return;

test_assert($item->id === $id, 'allocated load Id');
test_assert($item->title === "@title.2", 'allocated load Id Title');

//
// fields set
//

$collection->set_working_fields(['title']);
$item = $collection->load_only_id($id);
$item->text = "not-changed";
$item->save();

$query = $item->connection()->get_last_query();

test_assert(count($item->render()) === 2, 'WORKING-SET fields count');
test_assert(strpos($query, 'text') === false, 'WORKING-SET text unchanged');
