<?php
/**
 * Valenti Atelier - Create & Edit Garment Form (Light Mode & Responsive)
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$pdo = getDB();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) {
        setFlash('error', 'Product not found.');
        redirect('/admin/products.php');
    }
}

// Fetch all categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 1);
    $price = (float)($_POST['price'] ?? 0);
    $salePrice = (!empty($_POST['sale_price']) && is_numeric($_POST['sale_price'])) ? (float)$_POST['sale_price'] : NULL;
    $stock = max(0, (int)($_POST['stock'] ?? 10));
    $gender = sanitize($_POST['gender'] ?? 'Unisex');
    $sizes = sanitize($_POST['sizes'] ?? 'S,M,L,XL');
    $colors = sanitize($_POST['colors'] ?? 'Obsidian Black,Off White');
    $badge = sanitize($_POST['badge'] ?? '');
    if ($badge === '') $badge = NULL;
    $featured = isset($_POST['featured']) ? 1 : 0;
    $description = sanitize($_POST['description'] ?? '');

    // Slug generation
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

    // Image handling
    $imagePath = $product['image'] ?? 'assets/images/products/leather_bomber.jpg';
    if (!empty($_POST['preset_image'])) {
        $imagePath = sanitize($_POST['preset_image']);
    }

    // Check if an image file was uploaded
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['product_image']['tmp_name'];
        $fileName = basename($_FILES['product_image']['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        
        if (in_array($ext, $allowed)) {
            $newFileName = 'prod_' . uniqid() . '.' . $ext;
            $destination = BASE_PATH . '/assets/images/products/' . $newFileName;
            if (move_uploaded_file($fileTmp, $destination)) {
                $imagePath = 'assets/images/products/' . $newFileName;
            }
        }
    }

    if ($name !== '' && $price > 0) {
        if ($id > 0) {
            $upd = $pdo->prepare("
                UPDATE products SET 
                    category_id = ?, name = ?, slug = ?, description = ?, price = ?, 
                    sale_price = ?, gender = ?, sizes = ?, colors = ?, stock = ?, 
                    featured = ?, badge = ?, image = ?
                WHERE id = ?
            ");
            $upd->execute([
                $categoryId, $name, $slug, $description, $price,
                $salePrice, $gender, $sizes, $colors, $stock,
                $featured, $badge, $imagePath, $id
            ]);
            setFlash('success', "Garment '$name' updated successfully.");
        } else {
            $ins = $pdo->prepare("
                INSERT INTO products (
                    category_id, name, slug, description, price, 
                    sale_price, gender, sizes, colors, stock, 
                    featured, badge, image
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $ins->execute([
                $categoryId, $name, $slug, $description, $price,
                $salePrice, $gender, $sizes, $colors, $stock,
                $featured, $badge, $imagePath
            ]);
            setFlash('success', "New garment '$name' added to catalog.");
        }
        redirect('/admin/products.php');
    } else {
        setFlash('error', 'Please ensure garment name and price are valid.');
    }
}

$pageTitle = ($id > 0) ? "Edit Garment: " . $product['name'] : "Create New Garment Piece";
require_once __DIR__ . '/admin_header.php';
?>

<div style="max-width: 860px; margin: 0 auto;">
  
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
      <a href="/admin/products.php" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent-gold); display: inline-block; margin-bottom: 4px; font-weight: 700;">
        &larr; Back to Inventory
      </a>
      <h1 style="font-size: clamp(1.8rem, 3vw, 2rem); color: var(--text-primary);"><?php echo ($id > 0) ? 'Edit Garment Specification' : 'Add New Garment to Atelier'; ?></h1>
    </div>
  </div>

  <div class="admin-card">
    <form action="/admin/product_form.php<?php echo ($id > 0) ? '?id=' . $id : ''; ?>" method="POST" enctype="multipart/form-data">
      
      <div class="form-group">
        <label class="form-label">Garment Title / Nomenclature *</label>
        <input type="text" name="name" required placeholder="e.g. Architectural Wool Double-Breasted Blazer" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" class="form-control">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Department / Category *</label>
          <select name="category_id" class="form-control">
            <?php foreach ($categories as $c): ?>
              <option value="<?php echo $c['id']; ?>" <?php echo (isset($product['category_id']) && $product['category_id'] == $c['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($c['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Target Demographic *</label>
          <select name="gender" class="form-control">
            <option value="Unisex" <?php echo (isset($product['gender']) && $product['gender'] === 'Unisex') ? 'selected' : ''; ?>>Unisex</option>
            <option value="Men" <?php echo (isset($product['gender']) && $product['gender'] === 'Men') ? 'selected' : ''; ?>>Men's Collection</option>
            <option value="Women" <?php echo (isset($product['gender']) && $product['gender'] === 'Women') ? 'selected' : ''; ?>>Women's Collection</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Regular Retail Price (<?php echo CURRENCY_SYMBOL . ' ' . CURRENCY_CODE; ?>) *</label>
          <input type="number" step="0.01" name="price" required placeholder="340.00" value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label">Sale / Discount Price (<?php echo CURRENCY_SYMBOL . ' ' . CURRENCY_CODE; ?> - Optional)</label>
          <input type="number" step="0.01" name="sale_price" placeholder="295.00" value="<?php echo htmlspecialchars($product['sale_price'] ?? ''); ?>" class="form-control">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Inventory Units in Stock *</label>
          <input type="number" name="stock" required min="0" placeholder="15" value="<?php echo htmlspecialchars($product['stock'] ?? '15'); ?>" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label">Collection Badge</label>
          <select name="badge" class="form-control">
            <option value="">None</option>
            <option value="NEW" <?php echo (isset($product['badge']) && $product['badge'] === 'NEW') ? 'selected' : ''; ?>>NEW</option>
            <option value="HOT" <?php echo (isset($product['badge']) && $product['badge'] === 'HOT') ? 'selected' : ''; ?>>HOT</option>
            <option value="SALE" <?php echo (isset($product['badge']) && $product['badge'] === 'SALE') ? 'selected' : ''; ?>>SALE</option>
            <option value="BESTSELLER" <?php echo (isset($product['badge']) && $product['badge'] === 'BESTSELLER') ? 'selected' : ''; ?>>BESTSELLER</option>
            <option value="LIMITED" <?php echo (isset($product['badge']) && $product['badge'] === 'LIMITED') ? 'selected' : ''; ?>>LIMITED</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Available Sizes (Comma Separated)</label>
          <input type="text" name="sizes" placeholder="S,M,L,XL,XXL" value="<?php echo htmlspecialchars($product['sizes'] ?? 'S,M,L,XL'); ?>" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label">Color Variations (Comma Separated)</label>
          <input type="text" name="colors" placeholder="Obsidian Black,Oatmeal Heather,Sage" value="<?php echo htmlspecialchars($product['colors'] ?? 'Obsidian Black,Sand Dune Beige'); ?>" class="form-control">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Detailed Garment Description &amp; Fabric Specifications *</label>
        <textarea name="description" rows="4" required placeholder="Describe silhouette, drape, fabric composition, hardware, and tailoring..." class="form-control"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
      </div>

      <div class="form-row" style="align-items: center;">
        <div class="form-group" style="flex: 1;">
          <label class="form-label">Upload Garment Imagery (JPG, PNG, WebP, SVG)</label>
          <input type="file" name="product_image" accept="image/*" class="form-control">
        </div>

        <div class="form-group" style="flex: 1;">
          <label class="form-label">Or Choose Preset Atelier Asset</label>
          <select name="preset_image" class="form-control">
            <option value="">Keep current image</option>
            <option value="assets/images/products/leather_bomber.jpg">Leather Bomber (Outerwear)</option>
            <option value="assets/images/products/trench_coat.jpg">Trench Coat (Outerwear)</option>
            <option value="assets/images/products/tailored_blazer.jpg">Merino Blazer (Tailoring)</option>
            <option value="assets/images/products/wool_trousers.jpg">Pleated Trousers (Tailoring)</option>
            <option value="assets/images/products/oversized_hoodie.jpg">French Terry Hoodie (Knitwear)</option>
            <option value="assets/images/products/knit_sweater.jpg">Cashmere Turtleneck (Knitwear)</option>
            <option value="assets/images/products/linen_shirt.jpg">Camp Linen Shirt (Tops)</option>
            <option value="assets/images/products/minimalist_tee.jpg">Supima Essential Tee (Basics)</option>
            <option value="assets/images/products/silk_slip_dress.jpg">Mulberry Silk Slip Dress (Silk)</option>
            <option value="assets/images/products/cargo_pants.jpg">Tactical Ripstop Cargos (Bottoms)</option>
            <option value="assets/images/products/wrap_kimono.jpg">Raw Silk Haori Kimono (Avant-Garde)</option>
            <option value="assets/images/products/chelsea_boots.jpg">Tuscan Chelsea Boots (Footwear)</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: var(--text-primary); font-size: 0.9rem;">
          <input type="checkbox" name="featured" value="1" <?php echo (!empty($product['featured'])) ? 'checked' : ''; ?> style="accent-color: var(--accent-gold); width: 18px; height: 18px;">
          Highlight as Featured Piece on Storefront Homepage
        </label>
      </div>

      <div style="display: flex; gap: 12px; margin-top: 26px; flex-wrap: wrap;">
        <button type="submit" class="btn btn-primary" style="flex: 1; min-width: 200px;">
          <?php echo ($id > 0) ? 'Save Garment Modifications' : 'Publish Piece to Storefront'; ?>
        </button>
        <a href="/admin/products.php" class="btn btn-outline">Cancel</a>
      </div>

    </form>
  </div>

</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
