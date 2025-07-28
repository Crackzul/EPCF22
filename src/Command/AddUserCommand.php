<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:add-user',
    description: 'Ajoute un utilisateur à la base',
)]
class AddUserCommand extends Command
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Nom d\'utilisateur')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe (en clair pour ce test)')
            ->addArgument('no', InputArgument::OPTIONAL, 'Numéro utilisateur', '1');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setUsername($input->getArgument('username'));
        $user->setUsername1($input->getArgument('username'));
        $user->setEmail1($input->getArgument('email'));
        $user->setPassword1($input->getArgument('password'));
        $user->setNo($input->getArgument('no') ?? '1');
        $user->setRoles('ROLE_USER');

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('Utilisateur ajouté !');
        return Command::SUCCESS;
    }
}