<?php

namespace MS\UserBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use FOS\UserBundle\Command\CreateUserCommand as BaseCommand;

class CreateUserCommand extends BaseCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('ms:user:create')
            ->getDefinition()->addArguments(array(
                new InputArgument('firstname', InputArgument::REQUIRED, 'The firstname'),
                new InputArgument('lastname', InputArgument::REQUIRED, 'The lastname')
            ))
        ;
        $this->setHelp(<<<EOT
// L'aide qui va bien
EOT
        );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username   = $input->getArgument('username');
        $email      = $input->getArgument('email');
        $password   = $input->getArgument('password');
        $firstname  = $input->getArgument('firstname');
        $lastname   = $input->getArgument('lastname');
        $inactive   = $input->getOption('inactive');
        $superadmin = $input->getOption('super-admin');

        /** @var \FOS\UserBundle\Model\UserManager $user_manager */
        $user_manager = $this->getContainer()->get('fos_user.user_manager');

        /** @var \MS\UserBundle\Entity\User $user */
        $user = $user_manager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled((Boolean) !$inactive);
        $user->setSuperAdmin((Boolean) $superadmin);
        $user->setFirstName($firstname);
        $user->setLastName($lastname);

        $user_manager->updateUser($user);

        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));
    }


    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        parent::interact($input, $output);
        if (!$input->getArgument('firstname')) {

            $helper = $this->getHelper('question');

            $question = new Question('Please enter your Firstname : ');
            $question->setValidator(function ($value) {
                if (trim($value) == '') {
                    throw new \Exception('The firstname can not be empty');
                }

                return $value;
            });

            $firstname = $helper->ask($input, $output, $question);
            $input->setArgument('firstname', $firstname);
        }
        if (!$input->getArgument('lastname')) {

            $helper = $this->getHelper('question');

            $question = new Question('Please enter your Lastname : ');
            $question->setValidator(function ($value) {
                if (trim($value) == '') {
                    throw new \Exception('The lastname can not be empty');
                }

                return $value;
            });

            $lastname = $helper->ask($input, $output, $question);
            $input->setArgument('lastname', $lastname);
        }



    }
}
