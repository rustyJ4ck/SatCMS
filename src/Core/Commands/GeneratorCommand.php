<?php

namespace SatCMS\Core\Commands;

use \core;
use Symfony\Component\Console\Command\Command as CommandRunner;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratorCommand extends BaseCommand {

    protected function configure() {

        parent::configure();

        $this->setName('core:generator')
            ->setDescription('Generator (DB)')

            ->addArgument(
                'action', InputArgument::REQUIRED, 'Command to run, `render/update/create/...`'
            )

            ->addArgument(
                'models',
                InputArgument::IS_ARRAY,
                'Models list `sat.site users.sessions` or `*` for migrating all'
            )
        ;
    }

    /**
     * render|update|create model model ...
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \tf_exception
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

//require('../_tests/loader.php');

        $core = $this->core();
        $this->prepare_db($input->getOption('database'));

        $action = $input->getArgument('action');

        if (empty($action)) {
            throw new \tf_exception("empty action\n");
        }

        $ids = $input->getArgument('models');

        if (empty($ids)) {
            throw new \tf_exception("empty ID\n");
        }

        if ($ids[0] == '*') {
            $ids = Helpers\ModelEnumerator::find();
        }

        /*
         * $action = @$_SERVER['argv'][2] ? : 'render';
           if (in_array('--', $_SERVER['argv'])) {
           }
        */

        $output->writeln('action: ' . $action);

        /** @var collection_generator */
        $generator =
            \abs_collection::get_generator(
            # $id
            # 'core.mail_tpl'
            # 'core/texts'
            # 'core/logs'
            # 'core/config'
        );

        foreach ($ids as $mid) {

            $_mid = explode('.', trim($mid));

            if (count($_mid) == 1) {
                array_unshift($_mid, 'core');
                $container = core::selfie();
            } else {
                $container = core::module($_mid[0]);
            }

            if (!$container) {
                $output->writeln('skip ' . $mid);
                continue;
            }

            $method = 'get_' . $_mid[1] . '_handle';

            // allow model with params: get_model_handle | model('model')
            $obj =
                \functions::is_callable(array($container, $method))
                    ? $container->$method()
                    : $container->model($_mid[1]);

            $generator->append_object($obj);
        }

        $output->writeln($generator->render_table_structure(), OutputInterface::OUTPUT_RAW);

        if ($action == 'update') {
            $generator->update_table_structure();
        }

        if ($action == 'create') {
            $generator->update_table_structure(true);
        }

    }
}