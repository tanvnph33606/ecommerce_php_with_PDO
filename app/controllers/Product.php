<?php

class Product extends Controller
{
    private $productModel;
    private $categoryModel;
    private $req = null;
    private $res = null;
    public function __construct()
    {
        $this->req = new Request;
        $this->res = new Response;
        $this->productModel = $this->model('ProductModel');
        $this->categoryModel = $this->model('CategoryModel');
    }


    function Default()
    {
        $dataCateList = $this->categoryModel->getAllCategory() ?? [];
        $dataColor = $this->productModel->getAttributeProd(
            'Color'
        ) ?? [];
        $dataSize = $this->productModel->getAttributeProd('Size') ?? [];
        $this->view('layoutClient', [
            'title' => 'Danh mục sản phẩm',
            'currentPath' => 'product/',
            'pages' => 'product/productCategory',
            'dataCateList' => $dataCateList,
            'dataColor' => $dataColor,
            'dataSize' => $dataSize,
        ]);
    }

    function filterProd($cate)
    {
        echo $this->productModel->getAllProduct($cate) ?? json_encode([]);
    }

    function productDetail($id)
    {
        //Lay ra id tu chuoi slug
        $id = explode("-", $id);
        $id = end($id);

        $dataProd = $this->productModel->getOneProd($id) ?? [];
        $dataImageProd = $this->productModel->getImageProd($id) ?? [];
        // $dataVariant = $this->productModel->getVariantProd($id) ?? [];
        $dataProdRecent = $this->productModel->getProdRecently() ?? [];
        $dataVariant = $this->productModel->getAllProdVariants($id);

        if (!empty($dataVariant)) {
            $dataProdVariantsNew = [];
            foreach ($dataVariant as $item) {
                $idVariant = $item['id'];
                if (!isset($dataProdVariantsNew[$idVariant])) {
                    $dataProdVariantsNew[$idVariant] = [
                        'id' => $idVariant,
                        'title' => $item['title'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'discount' => $item['discount'],
                        'attribute_id' => $item['attribute_id'],
                        'display_name' => $item['display_name'],
                        'attribute_values' => [$item['attribute_value']],
                    ];
                } else {
                    $dataProdVariantsNew[$idVariant]['attribute_values'][] = $item['attribute_value'];
                }
            }

            foreach ($dataProdVariantsNew as &$item) {
                $item['attribute_values'] = implode(' - ', $item['attribute_values']);
            }

            $dataProdVariantsNew = array_values($dataProdVariantsNew);
        }


        $this->view('layoutClient', [
            'title' => $dataProd['title'],
            'thumb' => $dataProd['thumb'],
            'currentPath' => 'product/',
            'pages' => 'product/detailProduct',
            'dataProd' => $dataProd,
            'dataImageProd' => $dataImageProd,
            'dataVariant' => $dataProdVariantsNew,
            'dataProdRecent' => $dataProdRecent,
        ]);
    }

    function getVariantProdApi($variantId)
    {
        $data = $this->productModel->getOneProdVariant($variantId);
        $code = $data ? '200' : '400';
        echo $this->res->dataApi($code, '', $data);
    }

    function addRatingProd()
    {
        echo $this->productModel->addRatingProd();
    }
    function getAllRatingsProd($idProd)

    {
        echo $this->productModel->getAllRatings($idProd);
    }
}
