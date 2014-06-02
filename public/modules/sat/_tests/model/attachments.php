<?php

require __DIR__ . '/../../../core/_tests/loader.php';

/** @var tf_sat $sat */
$sat = core::module('sat');

//$imageAttachs = $nodes->get_behaviors();

$nodes = $sat->get_news_handle();

$sid = $nodes->make_attach_sid();

$id = $nodes->create([
    'title'         => 'Hello',
    'attach_sid'    => $sid
]);

$node = $nodes->get_last_item();

// attach

/** @var SatCMS\Modules\Sat\Classes\Behaviors\ImageAttachs $imagesBehavior */
$imagesBehavior = $node->behavior('Sat.ImageAttachs');

$imageAttachs = $imagesBehavior->get_attachs();

$imageAttachs->create([
    'pid'       => $id,
    'title'     => $node->id . '|' . functions::hash(microtime(1)),
    'ctype_id'  => $node->get_ctype_id()
]);


class testBehavior extends model_behavior {

    function remove_after() {
        test_assert(!$this->model->behavior('Sat.ImageAttachs')->get_attachs()->count());
    }

}

$node->add_behavior('test.behavior', new testBehavior);

/** @var SatCMS\Modules\Sat\Classes\Behaviors\Commentable $commentsBehavior */
$commentsBehavior = $node->behavior('Sat.Commentable');

$commentAttachs = $commentsBehavior->get_attachs();

for ($i = 0; $i <= 5; $i++) {
    $commentAttachs->create([
        'pid'       => $id,
        'comment'     => $node->id . '|' . functions::hash(microtime(1)),
        'ctype_id'  => $node->get_ctype_id()
    ]);
}

$node->remove();


$node = $nodes->load_only_id($id);

test_assert($imagesBehavior->get_attachs()->count() == 0);

$node = $nodes->get_last_item();

test_assert(!$node);

$node = $nodes->alloc();
$node->title = 'Привет Мир';
$node->save();

