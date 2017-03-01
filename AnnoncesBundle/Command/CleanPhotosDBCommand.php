<?php
namespace AnnoncesBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CleanPhotosDBCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('annonces:clean:photos')
			->setDescription('Delete from database unassociated photos')
			->setHelp('This command check all database photos and delete those which are unassociated and have expired.');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		
		$unassociatedPhotos = $em->getRepository('AnnoncesBundle:Photo')->findUnassociatedPhotos();
		
		foreach($unassociatedPhotos as $photo)
		{
			$em->remove($photo);
		}
		
		$em->flush();
		
		$output->writeln('Database successfully cleaned !');
	}
}
