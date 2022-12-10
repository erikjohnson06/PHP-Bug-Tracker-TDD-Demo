<?php
//Require autoloader for namespacing, custom Exception class, and POST processing scripts
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/src/Exception/Exception.php";

use App\Database\QueryBuilder;
use App\Entity\BugReport;
use App\Exception\BadRequestException;
use App\Repository\BugReportRepository;
use App\Helpers\DBQueryBuilderFactory;
use App\Logger\Logger;

/**
 * @var QueryBuilder $queryBuilder
 */
$queryBuilder = DBQueryBuilderFactory::make();

/**
 * @var BugReportRepository $repository
 */
$repository = new BugReportRepository($queryBuilder);

/**
 * @var Logger $logger
 */
$logger = new Logger;

//Process POST Requests
if ($_POST) {
    
    //Process POST Create Request
    if (isset($_POST["create"])) {

        $reportType = $_POST["reportType"];
        $email = $_POST["email"];
        $link = $_POST["link"];
        $message = $_POST["message"];

        try {

            $bugReport = new BugReport;
            $bugReport->setReportType($reportType)
                    ->setEmail($email)
                    ->setLink($link)
                    ->setMessage($message);
            /**
             * @var BugReport $newReport
             */
            $newReport = $repository->create($bugReport);
        } catch (Throwable $ex) {
            $logger->critical($ex->getMessage(), $_POST);
            throw new BadRequestException($ex->getMessage(), [$ex], 400);
        }

        $logger->info(
                "Bug report created",
                ["id" => $newReport->getId(), "type" => $newReport->getReportType()]
        );
    }

    //Process POST Update Request
    else if (isset($_POST["update"])) {

        $reportId = (int) $_POST["reportId"];
        $reportType = $_POST["reportType"];
        $email = $_POST["email"];
        $link = $_POST["link"];
        $message = $_POST["message"];

        try {

            /**
             * @var BugReport $bugReport
             */
            $bugReport = $repository->find($reportId);

            $bugReport->setReportType($reportType)
                    ->setEmail($email)
                    ->setLink($link)
                    ->setMessage($message);

            $newReport = $repository->update($bugReport);
        } catch (Throwable $ex) {
            $logger->critical($ex->getMessage(), $_POST);
            throw new BadRequestException($ex->getMessage(), [$ex], 400);
        }

        $logger->info(
                "Bug report updated",
                ["id" => $newReport->getId(), "type" => $newReport->getReportType()]
        );
    }

    //Process POST Delete Request
    else if (isset($_POST["delete"])) {

        $reportId = (int) $_POST["reportId"];

        try {

            /**
             * @var BugReport $bugReport
             */
            $bugReport = $repository->find($reportId);

            $repository->delete($bugReport);
        } catch (Throwable $ex) {
            $logger->critical($ex->getMessage(), $_POST);
            throw new BadRequestException($ex->getMessage(), [$ex], 400);
        }

        $logger->info(
                "Bug report deleted",
                ["id" => $reportId]
        );
    }
}

$bugReports = $repository->findAll();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bug Tracker Application</title>
        
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
        <link rel="stylesheet" href="src/Assets/css/styles.css">
        
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
        <script src="src/Assets/js/scripts.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>Manage <b>Bug Reports</b></h2>
                        </div>
                        <div class="col-sm-6">
                            <a href="#addBugReportModal" class="btn btn-success" data-toggle="modal">
                                <i class="material-icons">&#xE147;</i> 
                                <span>Add Report</span>
                            </a>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Report Type</th>
                            <th>Email</th>
                            <th style="width: 420px;">Message</th>
                            <th>Link</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($bugReports)): ?>
                            <?php /** @var \App\Entity\BugReport $report */ ?>
                            <?php foreach ($bugReports as $report): ?>
                                <tr>
                                    <td><?php echo $report->getReportType(); ?></td>
                                    <td><?php echo $report->getEmail(); ?></td>
                                    <td><?php echo $report->getMessage(); ?></td>
                                    <td><?php echo $report->getLink(); ?></td>
                                    <td>
                                        <a href="#updateReport-<?php echo $report->getId(); ?>" class="edit" data-toggle="modal">
                                            <i class="material-icons" data-toggle="tooltip" title="Edit">&#xE254;</i>
                                        </a>
                                        <a href="#deleteReport-<?php echo $report->getId(); ?>" class="delete" data-toggle="modal">
                                            <i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i>
                                        </a>

                                        <!-- Edit Modal HTML -->
                                        <div id="updateReport-<?php echo $report->getId(); ?>" class="modal fade">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="post">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Edit Report</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label>Report Type</label>
                                                                <select name="reportType" class="form-control" required>
                                                                    <option value="<?php echo $report->getReportType(); ?>"><?php echo $report->getReportType(); ?></option>
                                                                    <option value="video player">video player</option>
                                                                    <option value="audio">Audio</option>
                                                                    <option value="others">others</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Email</label>
                                                                <input type="email" name="email" class="form-control" value="<?php echo $report->getEmail(); ?>" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Message</label>
                                                                <textarea class="form-control" name="message" required><?php echo $report->getMessage(); ?></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Link</label>
                                                                <input type="url" class="form-control" name="link" value="<?php echo $report->getLink(); ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <input type="hidden" name="reportId" value="<?php echo $report->getId(); ?>">
                                                            <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                                                            <input type="submit" class="btn btn-info" name="update" value="Update">
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete Modal HTML -->
                                        <div id="deleteReport-<?php echo $report->getId(); ?>" class="modal fade">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="post">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Delete Report</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to delete these Records?</p>
                                                            <p class="text-warning"><small>This action cannot be undone.</small></p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <input type="hidden" name="reportId" value="<?php echo $report->getId(); ?>">
                                                            <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                                                            <input type="submit" class="btn btn-danger" name="delete" value="Delete">
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Report Modal HTML -->
        <div id="addBugReportModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <h4 class="modal-title">Submit Bug Report</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Report Type</label>
                                <select name="reportType" class="form-control" required>
                                    <option value="video player">Video Player</option>
                                    <option value="audio">Audio</option>
                                    <option value="others">others</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Message</label>
                                <textarea class="form-control" name="message" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="link">Link</label>
                                <input type="url" class="form-control" name="link" id="link">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                            <input type="submit" class="btn btn-success" name="create" value="Add">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>