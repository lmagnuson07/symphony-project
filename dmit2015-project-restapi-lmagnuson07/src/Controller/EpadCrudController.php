<?php

namespace App\Controller;

use App\Entity\Epad;
use App\Helper\EpadHelper;
use App\Helper\ParseCsv;
use App\Repository\EpadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/restapi/admin/epad', name: 'app_epad.')]
class EpadCrudController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $start = (int)$request->query->get('start');
        $max = (int)$request->query->get('max');

        try {
            $qb = $em->createQueryBuilder();
            $count = $qb->select('COUNT(epad.accountNumber)')
                ->from(Epad::class, 'epad')
                ->getQuery()
                ->getSingleScalarResult();

            $result = $qb->add('select', 'e')
                ->add('from', Epad::class . ' e')
                ->setFirstResult($start)
                ->setMaxResults($max)
                ->getQuery()
                ->getResult();

            return $this->json([
                "count" => $count,
                "data" => $result
            ]);
        } catch (NonUniqueResultException $e) {
            return $this->json([
                "count" => 0,
                "data" => null,
                "error" => true
            ]);
        }
    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $epad = json_decode($request->getContent());

        $epadObj = new Epad();
        $epadObj
            ->setAccountNumber($epad->accountNumber)
            ->setHouseNumber($epad->houseNumber ?? null)
            ->setStreetName($epad->streetName ?? null)
            ->setSuite($epad->suite ?? null)
            ->setAssessedValue($epad->assessedValue ?? null)
            ->setLongitude($epad->longitude ?? null)
            ->setLatitude($epad->latitude ?? null)
            ->setNeighbourhood($epad->neighbourhood ?? null)
            ->setNeighbourhoodId($epad->neighbourhoodId ?? null)
            ->setGarage($epad->garage ?? null)
            ->setWard($epad->ward ?? null)
            ->setAssessmentClass1($epad->assessmentClass1 ?? null);

        $em->persist($epadObj);
        $em->flush();

        return $this->json(
            $epadObj
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id, EpadRepository $er): JsonResponse
    {
        return $this->json(
            $er->find($id),
        );
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['PUT'])]
    public function edit(string $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $existingEpad = $em->getRepository(Epad::class)->find($id);
        $epad = json_decode($request->getContent());

        $existingEpad
            ->setAccountNumber($existingEpad->getAccountNumber())
            ->setHouseNumber($epad->houseNumber ?? null)
            ->setStreetName($epad->streetName ?? null)
            ->setSuite($epad->suite ?? null)
            ->setAssessedValue($epad->assessedValue ?? null)
            ->setLongitude($epad->longitude ?? null)
            ->setLatitude($epad->latitude ?? null)
            ->setNeighbourhood($epad->neighbourhood ?? null)
            ->setNeighbourhoodId($epad->neighbourhoodId ?? null)
            ->setGarage($epad->garage ?? null)
            ->setWard($epad->ward ?? null)
            ->setAssessmentClass1($epad->assessmentClass1 ?? null);

        $em->flush();

        return $this->json(
            $existingEpad
        );
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $epad = $em->getRepository(Epad::class)->find($id);
        $em->remove($epad);
        $em->flush();

        $epad = $em->getRepository(Epad::class)->find($id);

        if (!$epad) {
            return $this->json([
                'msg' => 'Successfully deleted record',
                'success' => true
            ]);
        } else {
            return $this->json([
                'msg' => 'The record still exists',
                'success' => true
            ]);
        }
    }

    #[Route('/import', name: 'import', methods: ['POST'])]
    public function import(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $data = $request->toArray();
        $importNumber = (int) $data['importNumber'];
        $batchSize = (int) $data['batchSize'];
        $count = 0;

        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '1800');
        $projectDir = $this->getParameter('kernel.project_dir');

        $headers = [
            "accountNumber", "suite", "houseNumber", "streetName", "garage",
            "neighbourhoodId", "neighbourhood", "ward", "assessedValue",
            "latitude", "longitude", "pointLocation", "blank1", "blank2", "blank3",
            "assessmentClass1", "assessmentClass2","assessmentClass3"
        ];

        try {
            // Define the input and output file paths
            $inputFilePath = $projectDir . '\\files\\Property_Assessment_Data__Current_Calendar_Year_.csv';
            $outputFolderPath = $projectDir . '\\files\\splitCsv\\';
            $rowsPerFile = 10000;
            $batchCount = 100;

            if ($importNumber === 0) {
                if (!is_dir($outputFolderPath)) {
                    mkdir($outputFolderPath, 0777, true);
                }

                // Initialize variables
                $splitIndex = 1;
                $rowCount = 0;
                $outputFile = fopen($outputFolderPath . "split_" . $splitIndex . ".csv", 'w');

                // Read the input CSV file line by line
                $inputFile = fopen($inputFilePath, 'r');
                if (!$inputFile) {
                    die("Failed to open input file.");
                }

                while (($row = fgetcsv($inputFile)) !== false) {
                    // Write row to the current split file
                    fputcsv($outputFile, $row);

                    $rowCount++;

                    // Check if the row count has reached the limit for a split file
                    if ($rowCount >= $rowsPerFile) {
                        fclose($outputFile);
                        $splitIndex++;
                        $rowCount = 0;
                        $outputFile = fopen($outputFolderPath . "split_" . $splitIndex . ".csv", 'w');
                    }
                }

                // Close the last split file
                fclose($outputFile);
                fclose($inputFile);
            }

            $files = scandir($outputFolderPath);

            if (is_array($files)) {
                // Remove "." and ".." entries from the list
                $files = array_diff($files, array('.', '..'));

                $files = array_chunk($files, $batchSize);
                $count = count($files);
                $files = $files[$importNumber];
            }

            foreach ($files as $fileName) {
                $filePath = $outputFolderPath . $fileName;

                if (is_file($filePath)) {
                    $contents = ParseCsv::parse(fileName: $filePath, headers: $headers);

                    $data = array_map(function ($item) {
                        return EpadHelper::mapToDto($item);
                    }, $contents);

                    for ($i = 0; $i < count($contents); $i++) {
                        $em->persist($data[$i]);

                        if (($i % $batchCount) == 0) {
                            $em->flush();
                            $em->clear();
                        }
                    }
                    $em->flush();
                    $em->clear();
                }
            }

            if ($count === $importNumber + 1) {
                function deleteDirectory ($outputFolderPath): bool {
                    if (!is_dir($outputFolderPath)) {
                        return false;
                    }

                    $dirHandle = opendir($outputFolderPath);
                    if (!$dirHandle) {
                        return false;
                    }

                    while (($file = readdir($dirHandle)) !== false) {
                        if ($file !== '.' && $file !== '..') {
                            $filePath = $outputFolderPath . '/' . $file;
                            if (is_dir($filePath)) {
                                deleteDirectory($filePath); // Recursively delete subdirectories
                            } else {
                                unlink($filePath); // Delete file
                            }
                        }
                    }

                    closedir($dirHandle);
                    rmdir($outputFolderPath);
                    return true;
                }

                $result = deleteDirectory($outputFolderPath);
                return $this->json([
                    "msg" => 'Successfully imported all data.',
                    "data" => [
                        "success" => true,
                        "chunkNumber" => $importNumber,
                        "totalCounts" => $count,
                        "complete" => true
                    ]
                ]);
            }

            return $this->json([
                "msg" => 'Successfully imported data. Import: ' . $importNumber + 1 . ' / ' . $count,
                "data" => [
                    "success" => true,
                    "chunkNumber" => $importNumber,
                    "totalCounts" => $count,
                    "complete" => false
                ]
            ]);
        } catch (Exception $ex) {
            return $this->json([
                "msg" => 'There was an error reading from the file or saving to the database.',
                "data" => [
                    "success" => false
                ]
            ]);
        }
    }
}
