body {
  background: #fff;
}

#wrapper {
  max-width: 700px;
  margin: 0 auto;
}

.header-container {
  padding: 30px 0 15px 0;
  text-align: center;
}

#body_content_inner table {
  width: 100%;
  max-width: 700px;
}

@media only screen and (max-width: 600px) {
  #body_content_inner {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  .aw-product-rows .image-container {
    border-bottom: 0 !important;
  }

  .aw-product-rows .title-container {
    display: block;
    padding-bottom: 30px !important;
    padding-left: 0 !important;
  }

  .aw-product-rows .image-container {
    width: 100%;
    display: flex;
    padding: 30px 0 13px 0 !important;
  }

  .aw-product-rows .image-container a,
  .aw-product-rows .image-container img {
    width: 100%;
  }

  .aw-product-image {
    width: 100%;
  }

  .aw-product-rows .product-title {
    font-size: 20px !important;
    margin-bottom: 14px;
  }

  .aw-product-rows .title-container .regular-price,
  .aw-product-rows .title-container .sale-price,
  .aw-product-rows .title-container .savings {
    font-size: 19px !important;
  }

  .aw-product-rows .price,
  .aw-product-rows .savings {
    display: inline;
  }

  .copy-container {
    padding: 0 !important;
  }

  .header-container img {
    width: 125px;
    max-width: 125px;
    height: auto;
  }

  .pre-footer-container a {
    font-size: 18px !important;
  }
}
