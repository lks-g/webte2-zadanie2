<?php

require_once './php/parse_data.php';

try {
    function test_parse_freefood() {
        $html = <<<EOD
        <html>
        <body>
          <h2>Menu type</h2>
          <table>
            <tr>
              <td>Dish 1</td>
              <td>10.50</td>
            </tr>
            <tr>
              <td>Dish 2</td>
              <td>8.50</td>
            </tr>
          </table>
        </body>
        </html>
        EOD;
      
        $expected_result = array(
          array(
            'name' => 'Dish 1',
            'menu_type' => 'Menu type',
            'price' => '10.50',
            'location' => 'Fakulta informatiky a informačných technológií STU, Ilkovičova 2, 841 04 Karlova Ves',
            'image_url' => null,
            'menu_date' => date('Y-m-d H:i:s')
          ),
          array(
            'name' => 'Dish 2',
            'menu_type' => 'Menu type',
            'price' => '8.50',
            'location' => 'Fakulta informatiky a informačných technológií STU, Ilkovičova 2, 841 04 Karlova Ves',
            'image_url' => null,
            'menu_date' => date('Y-m-d H:i:s')
          )
        );
      
        $result = parse_freefood($html);
      
        if ($result === $expected_result) {
          echo "parse_freefood test passed\n";
        } else {
          echo "parse_freefood test failed\n";
          echo "Expected:\n";
          print_r($expected_result);
          echo "Actual:\n";
          print_r($result);
        }
      }
} catch (Exception $e) {
  echo "Error: " . $e->getMessage();
}