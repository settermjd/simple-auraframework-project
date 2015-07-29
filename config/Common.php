<?php
namespace Aura\Framework_Project\_Config;

use Aura\Di\Config;
use Aura\Di\Container;
use Aura\Sql\ExtendedPdo;

class Common extends Config
{
    public function define(Container $di)
    {
        $di->set('aura/project-kernel:logger', $di->lazyNew('Monolog\Logger'));
    }

    public function modify(Container $di)
    {
        $this->modifyLogger($di);
        $this->modifyCliDispatcher($di);
        $this->modifyWebRouter($di);
        $this->modifyWebDispatcher($di);
    }

    protected function modifyLogger(Container $di)
    {
        $project = $di->get('project');
        $mode = $project->getMode();
        $file = $project->getPath("tmp/log/{$mode}.log");

        $logger = $di->get('aura/project-kernel:logger');
        $logger->pushHandler($di->newInstance(
            'Monolog\Handler\StreamHandler',
            array(
                'stream' => $file,
            )
        ));
    }

    protected function modifyCliDispatcher(Container $di)
    {
        $context = $di->get('aura/cli-kernel:context');
        $stdio = $di->get('aura/cli-kernel:stdio');
        $logger = $di->get('aura/project-kernel:logger');
        $dispatcher = $di->get('aura/cli-kernel:dispatcher');
        $dispatcher->setObject(
            'hello',
            function ($name = 'World') use ($context, $stdio, $logger) {
                $stdio->outln("Hello {$name}!");
                $logger->debug("Said hello to '{$name}'");
            }
        );
    }

    public function modifyWebRouter(Container $di)
    {
        $router = $di->get('aura/web-kernel:router');

        $router->add('hello', '/')
               ->setValues(array('action' => 'hello'));

        // Add an about page
        $router->add('about', '/about')
            ->setValues(array('action' => 'about'));

        // Add a sales data display page
        $router->add('data-view-sales', '/data/view/sales')
            ->setValues(array('action' => 'data-view-sales'));
    }

    public function modifyWebDispatcher($di)
    {
        $dispatcher = $di->get('aura/web-kernel:dispatcher');

        $dispatcher->setObject('hello', function () use ($di) {
            $response = $di->get('aura/web-kernel:response');
            $response->content->set('Hello World!');
        });

        $dispatcher->setObject('about', function () use ($di) {
            $view_factory = new \Aura\View\ViewFactory;
            $view = $view_factory->newInstance();

            $layout_registry = $view->getLayoutRegistry();
            $layout_registry->set('default', './../src/templates/default.layout.php');

            $view_registry = $view->getViewRegistry();
            $view_registry->set('about', './../src/templates/about.php');
            $view->setView('about');
            $view->setLayout('default');

            $response = $di->get('aura/web-kernel:response');
            $response->content->set($view());
        });

        $dispatcher->setObject('data-view-sales', function () use ($di) {
            $view_factory = new \Aura\View\ViewFactory;
            $view = $view_factory->newInstance();

            $pdo = new ExtendedPdo(
                'sqlite:./../db/database.sqlite'
            );

            $extended_pdo = new ExtendedPdo($pdo);
            $stm = '
                SELECT t.TrackId, sum(il.unitprice) as "TotalSales", t.Name as "TrackName", g.Name as Genre, a.Title as "AlbumTitle", at.Name as "ArtistName"
                from InvoiceLine il
                inner join track t on (t.TrackId = il.TrackId)
                INNER JOIN genre g on (g.GenreId = t.GenreId)
                inner join album a on (a.AlbumId = t.AlbumId)
                INNER JOIN artist at on (at.ArtistId = a.ArtistId)
                WHERE g.Name like :genre
                group by t.TrackId
                HAVING at.Name = :artist_name
                order by sum(il.UnitPrice) desc, t.Name asc
            ';

            $bind = array(
                'genre' => 'TV%',
                'artist_name' => 'Lost'
            );

            $sth = $pdo->prepare($stm);
            $sth->execute($bind);

            $layout_registry = $view->getLayoutRegistry();
            $layout_registry->set('default', './../src/templates/sales.layout.php');

            $view_registry = $view->getViewRegistry();
            $view_registry->set('sales-data', './../src/templates/data/sales/view.php');
            $view->setView('sales-data');
            $view->setLayout('default');

            // the "sub" template
            $view_registry->set('_result', './../src/templates/data/sales/result.php');

            $view->setData([
                'results' => $pdo->fetchObjects($stm, $bind, '\DatabaseObjects\Entity\SalesData')
            ]);

            $response = $di->get('aura/web-kernel:response');
            $response->content->set($view());
        });
    }
}
