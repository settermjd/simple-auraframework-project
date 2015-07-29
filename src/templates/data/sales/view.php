<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
            <ul class="nav nav-sidebar">
                <li class="active"><a href="#">Overview <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
        <div class="col-sm-9 col-md-12 col-md-offset-1 main">
            <h1 class="page-header">Dashboard</h1>

            <h2 class="sub-header">Sales Data</h2>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Total Sales</th>
                        <th>Track Name</th>
                        <th>Genre</th>
                        <th>Album Title</th>
                        <th>Artist Name</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($this->results as $result) {
                        echo $this->render('_result', array(
                                'result' => $result,
                            )
                        );
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6">
                                <?php
                                    printf("Total Results: %d", count($this->results));
                                ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
