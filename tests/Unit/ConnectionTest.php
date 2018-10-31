<?php

namespace ZipkinDoctrine\Tests;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Driver\Statement;
use Zipkin\TracingBuilder;
use Zipkin\Reporters\InMemory;
use ZipkinDoctrine\Connection;
use PHPUnit\Framework\TestCase;
use Zipkin\Samplers\BinarySampler;

final class ConnectionTest extends TestCase
{
    /**
     * @dataProvider useTracer
     */
    public function testExecuteQuerySuccess($useTracer)
    {
        list($tracer, $reporter, $conn) = $this->buildDependencies();
            
        if ($useTracer) {
            $conn->setTracer($tracer);
        }

        $query = 'SELECT 1';
        $stmt = $conn->executeQuery($query);

        $tracer->flush();
        $spans = $reporter->flush();

        $this->assertInstanceOf(Statement::class, $stmt);
        if ($useTracer) {
            $this->assertCount(1, $spans);
            $this->assertArraySubset([
                'name' => 'sql/query',
                'tags' => [
                    'sql.query' => $query
                ]
            ], $spans[0]->toArray());
        } else {
            $this->assertCount(0, $spans);
        }
    }

    /**
     * @dataProvider useTracer
     */
    public function testExecuteUpdateSuccess($useTracer)
    {
        list($tracer, $reporter, $conn) = $this->buildDependencies();
        /* Should create the table before we execute the update */
        $conn->executeQuery('CREATE TABLE things(id INTEGER PRIMARY KEY ASC, deleted integer)');

        if ($useTracer) {
            $conn->setTracer($tracer);
        }

        $query = 'UPDATE things SET deleted = 1';
        $affectedRows = $conn->executeUpdate($query);

        $tracer->flush();
        $spans = $reporter->flush();

        $this->assertTrue(is_int($affectedRows));

        if ($useTracer) {
            $this->assertCount(1, $spans);
            $this->assertArraySubset([
                'name' => 'sql/update',
                'tags' => [
                    'sql.query' => $query
                ]
            ], $spans[0]->toArray());
        } else {
            $this->assertCount(0, $spans);
        }
    }

    /**
     * @dataProvider useTracer
     */
    public function testExecSuccess($useTracer)
    {
        list($tracer, $reporter, $conn) = $this->buildDependencies();

        if ($useTracer) {
            $conn->setTracer($tracer);
        }

        $statement = 'SELECT 1';
        $affectedRows = $conn->exec($statement);

        $tracer->flush();
        $spans = $reporter->flush();

        $this->assertTrue(is_int($affectedRows));
        if ($useTracer) {
            $this->assertCount(1, $spans);
            $this->assertArraySubset([
                'name' => 'sql/exec',
                'tags' => [
                    'sql.query' => $statement
                ]
            ], $spans[0]->toArray());
        } else {
            $this->assertCount(0, $spans);
        }
    }

    /**
     * @return array
     */
    public function useTracer()
    {
        return [
            [true],
            [false],
        ];
    }

    private function buildDependencies()
    {
        $reporter = new InMemory();
        $tracer = TracingBuilder::create()
            ->havingSampler(BinarySampler::createAsAlwaysSample())
            ->havingReporter($reporter)
            ->build()
            ->getTracer();

        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = [
                'user' => 'root',
                'password' => 'root',
                'driver' => 'pdo_sqlite',
                'memory' => true,
                'wrapperClass' => Connection::class,
            ];
    
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        return [$tracer, $reporter, $conn];
    }
}
