<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Test\Unit\Ui\Component\Listing\Column;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Sales\Ui\Component\Listing\Column\CustomerGroup;

/**
 * Class CustomerGroupTest
 */
class CustomerGroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CustomerGroup
     */
    protected $model;

    /**
     * @var GroupRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupRepository;

    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->groupRepository = $this->getMockForAbstractClass('Magento\Customer\Api\GroupRepositoryInterface');
        $this->model = $objectManager->getObject(
            'Magento\Sales\Ui\Component\Listing\Column\CustomerGroup',
            ['groupRepository' => $this->groupRepository]
        );
    }

    public function testPrepareDataSource()
    {
        $itemName = 'itemName';
        $oldItemValue = 'oldItemValue';
        $newItemValue = 'newItemValue';
        $dataSource = [
            'data' => [
                'items' => [
                    [$itemName => $oldItemValue]
                ]
            ]
        ];

        $group = $this->getMockForAbstractClass('Magento\Customer\Api\Data\GroupInterface');
        $group->expects($this->once())
            ->method('getCode')
            ->willReturn($newItemValue);
        $this->groupRepository->expects($this->once())
            ->method('getById')
            ->with($oldItemValue)
            ->willReturn($group);

        $this->model->setData('name', $itemName);
        $this->model->prepareDataSource($dataSource);
        $this->assertEquals($newItemValue, $dataSource['data']['items'][0][$itemName]);
    }
}
