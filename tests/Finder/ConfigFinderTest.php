<?php

namespace SymfonyAutoDiYml\Tests\Finder;

use SymfonyAutoDiYml\Finder\ComposerParser;
use SymfonyAutoDiYml\Finder\ConfigFinder;
use SymfonyAutoDiYml\Finder\YamlParser;
use SymfonyAutoDiYml\Tests\BaseTestCase;

class ConfigFinderTest extends BaseTestCase
{
    public function testSuccessfulParsing()
    {
        $composerParserMock = $this->getComposerParserMock();

        $parsedYaml = [
            "parameters" => [
                "symfony-yml-builder" => [
                    "bundles" => ["Gts/ApiBundle"]
                ]
            ]
        ];

        $yamlParserMock = $this->getYamlParserMock($parsedYaml);

        $configFinder = new ConfigFinder($composerParserMock, $yamlParserMock);
        $realYaml = $configFinder->getConfigYml();

        $this->assertEquals($parsedYaml, $realYaml);
    }

    /**
     * ConfigFinder must return empty list if parameter does not exists
     */
    public function testNonExistingParameter()
    {
        $composerParserMock = $this->getComposerParserMock();

        $yamlParserMock = $this->getYamlParserMock(["parameters" => []]);

        $configFinder = new ConfigFinder($composerParserMock, $yamlParserMock);
        $realYaml = $configFinder->getConfigYml();

        $this->assertEquals(
            [
                'parameters' => [
                    'symfony-yml-builder' => [
                        'bundles' => [],
                    ]
                ]
            ],
            $realYaml
        );
    }

    /**
     * ConfigFinder must throw exception if parameter is not an array
     *
     * @expectedException \InvalidArgumentException
     */
    public function testNotValidParameter()
    {
        $parsedYaml = [
            'parameters' => [
                'symfony-yml-builder' => [
                    'bundles' => "wrongval",
                ]
            ]
        ];

        $composerParserMock = $this->getComposerParserMock();
        $yamlParserMock = $this->getYamlParserMock($parsedYaml);

        $configFinder = new ConfigFinder($composerParserMock, $yamlParserMock);
        $configFinder->getConfigYml();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getComposerParserMock()
    {
        $mock = $composerParserMock = $this
            ->getMockBuilder(ComposerParser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock
            ->expects($this->exactly(1))
            ->method("getSymfonyAppDir")
            ->willReturn("app");

        return $mock;
    }

    /**
     * @param $returningYaml
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getYamlParserMock($returningYaml)
    {
        $yamlParserMock = $this
            ->getMockBuilder(YamlParser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $yamlParserMock
            ->expects($this->exactly(1))
            ->method("parse")
            ->willReturn($returningYaml);

        return $yamlParserMock;
    }
}
