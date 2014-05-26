<?php

namespace SatCMS\Modules\Core\Commands;

/*
use SatCMS\Modules\Core\Abstract\Runner as CommandRunner;
*/

use loader, core;
use Symfony\Component\Console\Command\Command as CommandRunner;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Finder\Finder;

class InstallCommand extends CommandRunner {

    /** @var  core */
    private $_core;

    protected function configure() {
        $this->setName('core:install')
            ->setDescription('Fresh install');
    }

    function migrate($output) {

        $root = loader::get_public() . loader::DIR_MODULES . '*/classes';
        $finder = new Finder();
        $finder->directories()->in($root)->name('*')->depth('== 0');

        $generator = \abs_collection::get_generator();

        /** @var \SplFileInfo  $file */
        foreach ($finder as $file) {

            preg_match('@(?P<module>[\w_+]*)[\\\/]classes[\\\/](?P<model>[\w_+]*)$@', $file, $matches);

                if ('core' == $matches['module'] || core::modules()->is_registered($matches['module'])) {

                $model = $matches['module'] . '.' . $matches['model'];

                $generator->append_object(
                    $this->_core->model($model), $model
                );

                $output->writeln('...' . $model);

            }
        }

        $output->writeln($generator->update_table_structure(true));
    }

    function create_admin($input, $output) {

        $dialog = $this->getHelperSet()->get('dialog');

        $output->writeln('');
        $output->writeln('<info>Configuration:</info>');
        $output->writeln('');


        $login = $dialog->ask(
            $output,
            'Please enter superuser login: ',
            'admin'
        );

        $password = $dialog->ask(
            $output,
            'Enter superuser password: ',
            'admin' . rand(666, 999)
        );

        core::module('users')->get_users_handle()->register_new_user(array(
              'nick'     => 'Admin'
            , 'login'    => $login
            , 'email'    => 'admin@localhost.local'
            , 'password' => $password
            , 'active'   => true
            )
            , 'admin'
        );

        $domain = $dialog->ask(
            $output,
            'Enter site domain: ',
            'localhost'
        );

        core::module('sat')->get_site_handle()->create(array(
            'title' => 'Default site',
            'domain' => $domain,
            'active' => true
        ));

    }

    /**
     * render|update|create model model ...
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \tf_exception
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        $this->_core = \core::get_instance(true);

        $dialog = $this->getHelperSet()->get('dialog');

        $output->writeln('<info>Database configuration:</info>');
        $output->writeln('');

        $output->writeln(var_export($this->_core->db->get_config(), 1));

        $output->writeln('');
        $output->writeln('<fg=cyan>In case of sqlite you must `touch path/to/database` by yourself</fg=cyan>');
        $output->writeln('');

        if (!$dialog->askConfirmation(
            $output,
            '<question>This will remove all data in database. Continue with this action? [y/n] </question>',
            false
        )) {
            return;
        }

        $this->migrate($output);
        $this->create_admin($input, $output);

        $output->writeln('All done.');

        return;
    }
}