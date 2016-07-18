<?php namespace Acme;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use GuzzleHttp\ClientInterface;
use ZipArchive;

class NewCommand extends Command {

	private $client;

	public function __construct(ClientInterface $client)
	{
		$this->client = $client;

		parent::__construct();
	}

	public function configure() 
	{
		$this->setName('new')
			 ->setDescription('Create a new laravel application.')
			 ->addArgument('name', InputArgument::REQUIRED);
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		// assert that the folder doesn't exist
		$directory = getcwd() . '/' . $input->getArgument('name');
		$this->assertApplicationDoesNotExist($directory, $output);

		// download nightly version of laravel
		// extract zip file
		$this->download($zipFile = $this->makeFileName())
			 ->extract($zipFile, $directory)
			 ->cleanUp($zipFile);

		// alert the user that they are ready to go
		$output->writeln('Application ready!!');
	}

	private function assertApplicationDoesNotExist($directory, OutputInterface $output) {
		if (is_dir($directory)) {
			$output->writeln('<error>Application already exists.</error>');
			exit(1);
		}
	}

	private function makeFileName()
	{
		return getcwd() . '/laravel_' . md5(time().uniqid()) . '.zip';
	}

	private function download($zipFile)
	{
		$response = $this->client->get('http://cabinet.laravel.com/latest.zip')->getBody();

		file_put_contents($zipFile, $response);

		return $this; // to continue chaining
	}

	private function extract($zipFile, $directory)
	{
		$archive = new ZipArchive;

		$archive->open($zipFile);

		$archive->extractTo($directory);

		$archive->close();

		return $this; // to continue chaining
	}

	private function cleanUp($zipFile)
	{
		@chmod($zipFile, 0777);	// supressing warnings
		@unlink($zipFile);		// supressing warnings

		return $this;	// to continue chaining
	}
}