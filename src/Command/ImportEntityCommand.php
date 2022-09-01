<?php
// src/Command/ImportEntityCommand.php
namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use App\Kernel;
use App\Repository\ProductRepository;
use App\Service\CategoryService;
use App\Service\ProductService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[AsCommand(
    name: 'app:import-entity',
    description: 'Insert/Update the entity',
    hidden: false,
    aliases: ['app:import-entity']
)]
class ImportEntityCommand extends Command
{
    const TYPE_PRODUCT = 'product';
    const TYPE_CATEGORY = 'category';
    protected $container;

    public function __construct(Kernel $kernel, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->em = $em;
        $this->container = $kernel->getContainer();

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // configure an argument
            ->addOption(
                'path', 'p', InputOption::VALUE_REQUIRED, 'The filepath of the json file.')
            ->addOption('type', 't', InputOption::VALUE_REQUIRED, 'Entity name. E.g Product')// ...
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $output->writeln([
            'Entity Creator',
            '============',
            '',
        ]);
        $count = 0;

        $type = $input->getOption('type');
        $filepath = $input->getOption('path');

//        $service = $this->getEntityService($type);
        $entityName = $this->getEntityName($type);

        $this->repository = $this->em->getRepository($entityName);

        if (!$entityName) {
            $output->write('The entity not found.');
            return Command::FAILURE;
        }

        $data = $this->readJsonFile($filepath);
        if (empty($data)) {
            $output->write('No data to import.');
            return Command::FAILURE;

        }
        foreach ($data as $index => $value) {

            $is_created = $this->repository->createOrUpdateFromArray($value);
            if ($is_created) {
                $count++;
            }
        }

        if (!$data) {
            $output->write('The file not found or empty.');
            return Command::FAILURE;
        }
        $output->writeln("$count $type has been created/updated!");

        $output->write('Done!');

        return Command::SUCCESS;
    }


    protected function getEntityName($type)
    {
        switch ($type) {
            case self::TYPE_PRODUCT:
                return Product::class;
            case self::TYPE_CATEGORY:
                return Category::class;
            default:
                return FALSE;
        }

    }

    protected function readJsonFile($filepath)
    {
        $content = file_get_contents($filepath);
        return json_decode($content, TRUE);
    }
}
