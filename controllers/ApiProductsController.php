<?php

use Dc\Api\ApiConsumer;
use Dc\Core\Entity;
use Dc\Export;
use Illuminate\Support\Arr;
use Inovim\ApiConsumer\Api\ApiResultCollection;
use Inovim\ApiConsumer\Api\BaseApi;
use Inovim\ApiConsumer\Exceptions\NotFoundException;
use Inovim\ApiConsumer\Models\Data;
use Inovim\ApiConsumer\Models\Relationship;

class Addresses_ApiProductsController extends Dc_Core_Controller_Modules_Card_Base
{
    protected $_productsScope = null;
    protected $_paginatorItemsPerPage = 25;

    public function init()
    {
        $this->_entityClassName = Entity\Product::class;
        $this->_exportClassName = Export\Products::class;

        $this->allowedActions = [
            'create',
            'validation',
            'update',
            'children',
        ];

        parent::init();

        $this->_productsScope = $this->applicationScope->productsScope();
    }

    /**
     * @return bool|void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getDataForCreateAndUpdate()
    {
        if (empty($this->getPoi())) {
            return false;
        }

        $this->view->poiId = $this->getPoi();

        $customerId = $this->applicationScope->currentProject();

        if ($customerId === null) {
            $this->_helper->flashMessenger([
                'error' => $this->view->translate('Please select a project before continueing.')
            ]);
            return false;
        }

        try {
            $configuration = (new BaseApi(ApiConsumer::getInstance(), 'api', "customers/{$customerId}/configuration"))
                ->filter('segment', 'settings')
                ->getAll();

            $this->view->customerSettings = $configuration[0]->attributes;
        } catch (Exception $e) {
            $this->_helper->flashMessenger(["error" => $this->getErrorMessage($e)]);
            return;
        }

        $configurationProfiles = Arr::get($this->view->customerSettings, 'configuration.profiles');
        $categories = implode(",", array_keys($configurationProfiles));

        try {
            $apiConnector = (new BaseApi(ApiConsumer::getInstance(), 'api', 'profiles'))
                ->version('v2')
                ->filter('customer', $customerId)
                ->filter('category', $categories)
                ->getAll();

            /** @var ApiResultCollection $apiConnector */
            $profilesResult = $apiConnector
                ->map(function (Data $profile) {
                    $profileCategory = $profile->get('attributes', [])['category'] ?? null;

                    $optionsRelations = collect($profile->relationships->get('options'));

                    $optionAttributeKeys = $optionsRelations
                        ->pluck('attributes.category')
                        ->map(function (string $optionCategory) use ($profileCategory) {
                            return str_replace("{$profileCategory}_", '', $optionCategory);
                        });

                    $attributeValues = Arr::only($profile->get('attributes'), $optionAttributeKeys->toArray());

                    return [
                        'id' => $profile->id,
                        'type' => $profile->type,
                        'category' => $profile->getAttribute('category'),
                        'label' => implode(' ', array_filter($attributeValues)),
                        'options' => collect($optionsRelations)
                            ->map(function (Relationship $optionRelationship) use ($profile, $profileCategory) {
                                $optionRelationship->offsetSet(
                                    'label',
                                    $profile->getAttribute(str_replace(
                                        "{$profileCategory}_",
                                        '',
                                        $optionRelationship->getAttribute('category')
                                    ))
                                );

                                return $optionRelationship;
                            }),
                    ];
                });

            $this->view->customerProfiles = $profilesResult;
        } catch (Exception $e) {
            $this->_helper->flashMessenger(["error" => $this->getErrorMessage($e)]);
            return;
        }

        $productOptionCategories = collect(Arr::get($this->view->customerSettings, 'configuration.products'))
            ->values()
            ->flatten(1)
            ->map(function (array $data) {
                return collect(Arr::get($data, 'options'))->pluck('category');
            })
            ->flatten(1)
            ->all();

        $categories = implode(",", $productOptionCategories);

        try {
            $apiConnector = (new BaseApi(ApiConsumer::getInstance(), 'api', 'options'))
                ->filter('category', $categories);

            /** @var ApiResultCollection $profilesResult */
            $profilesResult = $apiConnector->getAll()
                ->map(function (Data $profile) {
                    return [
                        'id' => $profile->id,
                        'type' => $profile->type,
                        'category' => $profile->getAttribute('category'),
                        'name' => $profile->getAttribute('name'),
                    ];
                });

            $this->view->customerProductOptions = $profilesResult;
        } catch (Exception $e) {
            $this->_helper->flashMessenger(['error' => $this->getErrorMessage($e)]);
            return;
        }
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function createAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getDataForCreateAndUpdate();
            $this->view->contentTitle = $this->_createContentTitle();
            return;
        }

        $this->disableViewAndLayout();

        foreach ($this->getRequest()->getPost() as $productToCreate) {
            try {
                $body = $this->getBody(json_decode($productToCreate), $this->getPoi());

                ApiConsumer::getInstance()->post('api/v1/products', $body);
            } catch (Exception $e) {
                $this->_helper->json(['error' => $this->getErrorMessage($e)]);
                return;
            }
        }

        $this->_helper->json(['ok' => true]);
    }

    /**
     *
     */
    public function validationAction()
    {
        $this->disableViewAndLayout();

        parse_str(parse_url($this->_request->getRequestUri())['query'], $params);

        try {
            $apiConnector = (new BaseApi(ApiConsumer::getInstance(), 'api', 'validators'))
                ->filter('profile', $params['matching_profile'])
                ->filter('priority', 'max');

            $validatorsResult = $apiConnector->getAll()
                ->map(function (Data $profile) {
                    return [
                        'id' => $profile->id,
                        'type' => $profile->type,
                        'attributes' => $profile->getEntityAttributes(),
                    ];
                });
        } catch (NotFoundException $e) {
        } catch (Exception $e) {
            $this->_helper->json(["error" => $this->getErrorMessage($e)]);
            return;
        }

        $this->_helper->json($validatorsResult ?? []);
    }

    /**
     *
     */
    public function childrenAction()
    {
        $this->disableViewAndLayout();

        parse_str(parse_url($this->_request->getRequestUri())['query'], $params);

        try {
            $uri = "profiles/{$params['matching_profile']}/children";

            $apiConnector = (new BaseApi(ApiConsumer::getInstance(), 'api', $uri))
                ->version('v2');

            $childrenResult = $apiConnector->getAll()
                ->map(function (Data $child) {
                    return [
                        'fraction' => $child->getEntityAttributes()['fraction'],
                        'volume' => $child->getEntityAttributes()['volume'],
                        'volume_percentage' => $child->getEntityAttributes()['volume_percentage'],
                    ];
                });
        } catch (NotFoundException $e) {
        } catch (Exception $e) {
            $this->_helper->json(["error" => $this->getErrorMessage($e)]);
            return;
        }

        $this->_helper->json($childrenResult ?? []);
    }

    /**
     * @throws Zend_Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function updateAction()
    {
        $productId = (int)$this->_getParam('id');

        if ($this->getRequest()->isPost()) {
            $this->disableViewAndLayout();

            try {
                $productToUpdate = $this->getRequest()->getPost()[0];
                $body = $this->getBody(json_decode($productToUpdate), $this->getPoi(), $productId);

                ApiConsumer::getInstance()->patch("api/v1/products?filter[link_id]=${productId}", $body);
            } catch (Exception $e) {
                $this->_helper->json(["error" => $this->getErrorMessage($e)]);
                return;
            }

            $this->_helper->json(['ok' => true]);
            return;
        }

        $this->getDataForCreateAndUpdate();

        $this->view->productToEdit = [];

        $apiConnector = (new BaseApi(ApiConsumer::getInstance(), 'api', "products?filter[link_id]=${productId}"));

        $product = $apiConnector->getAll()
            ->map(function (Data $profile) {
                return [
                    'id' => $profile->id,
                    'type' => $profile->type,
                    'attributes' => $profile->getEntityAttributes(),
                    'relationships' => $profile->relationships,
                ];
            });

        $this->view->productToEdit = $product;

        $this->view->contentTitle = $this->_updateContentTitle();
    }

    /**
     * @param $productToCreateOrUpdate
     * @param $poiId
     * @param null $productId
     * @return array
     */
    public function getBody($productToCreateOrUpdate, $poiId, $productId = null)
    {
        $data = [
            "data" => [
                "type" => "product",
                "attributes" => [
                    "category" => $productToCreateOrUpdate->data->attributes->category ?? null,
                    "chipcode" => $productToCreateOrUpdate->data->attributes->chipcode ?? null,
                    "barcode" => $productToCreateOrUpdate->data->attributes->barcode ?? null,
                    "labelcode" => $productToCreateOrUpdate->data->attributes->labelcode ?? null,
                    "activation_date" => $productToCreateOrUpdate->data->attributes->activation_date ?? null,
                    "properties" => (array)$productToCreateOrUpdate->data->attributes->properties ?? [],
                ],
                "relationships" => [
                    "poi" => [
                        "data" => [
                            "type" => "poi",
                            "id" => $poiId ?? null
                        ]
                    ],
                    "profile" => [
                        "data" => [
                            "type" => "profile",
                            "id" => $productToCreateOrUpdate->data->relationships->profile->data->id ?? null
                        ]
                    ]
                ]
            ]
        ];

        if ($productId) {
            $data['data']['id'] = $productId;
        }

        return $data;
    }

    /**
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getPoi()
    {
        try {
            $linkId = $this->_entityForCreate()->card->id;

            return ApiConsumer::getInstance()
                ->get("api/v1/pois?filter[link_id]={$linkId}&filter[category]=residential_poi")
                ->getData()[0]
                ->id;
        } catch (Exception $e) {
            $this->_helper->flashMessenger(['error' => $this->getErrorMessage($e)]);
            return [];
        }
    }

    /**
     * @param Exception $e
     * @return string
     */
    public function getErrorMessage(Exception $e)
    {
        if (method_exists($e, 'getValidationErrors')) {
            return $e->getValidationErrors();
        }

        if ($e->getPrevious()->getResponse()) {
            $errorContent = json_decode($e->getPrevious()->getResponse()->getBody()->getContents());
            $uri = $e->getPrevious()->getRequest()->getUri();

            $path = $uri->getPath();
            $query = "?{$uri->getQuery()}";

            return isset($errorContent->errors)
                ? "{$errorContent->errors->code} {$errorContent->errors->title}: {$errorContent->errors->detail} ({$path}{$query})"
                : $errorContent->message ?? "something went wrong ... please try again.";
        }

        return "Error {$e->getCode()}: {$e->getMessage()}";
    }
}
