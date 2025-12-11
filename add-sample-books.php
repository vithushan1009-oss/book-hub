<?php
/**
 * Script to add 10 sample books with images to the BookHub database
 * Run this script once to populate the database with sample data
 */

require_once __DIR__ . '/src/config.php';

$conn = getDbConnection();

// Sample books data
$books = [
    [
        'title' => 'The Great Gatsby',
        'author' => 'F. Scott Fitzgerald',
        'isbn' => '978-0743273565',
        'genre' => 'Fiction',
        'description' => 'The Great Gatsby is a 1925 novel by American writer F. Scott Fitzgerald. Set in the Jazz Age on Long Island, the novel depicts narrator Nick Carraway\'s interactions with mysterious millionaire Jay Gatsby.',
        'book_type' => 'physical',
        'total_quantity' => 5,
        'rental_price_per_day' => 2.50,
        'purchase_price' => null,
        'publisher' => 'Scribner',
        'publication_date' => '1925-04-10',
        'language' => 'English',
        'pages' => 180
    ],
    [
        'title' => 'To Kill a Mockingbird',
        'author' => 'Harper Lee',
        'isbn' => '978-0061120084',
        'genre' => 'Fiction',
        'description' => 'To Kill a Mockingbird is a novel by the American author Harper Lee. It was published in 1960 and was instantly successful. The novel is renowned for its warmth and humor.',
        'book_type' => 'physical',
        'total_quantity' => 8,
        'rental_price_per_day' => 3.00,
        'purchase_price' => null,
        'publisher' => 'Harper Perennial',
        'publication_date' => '1960-07-11',
        'language' => 'English',
        'pages' => 336
    ],
    [
        'title' => '1984',
        'author' => 'George Orwell',
        'isbn' => '978-0451524935',
        'genre' => 'Dystopian',
        'description' => '1984 is a dystopian social science fiction novel and cautionary tale by English writer George Orwell. It was published on 8 June 1949 as Orwell\'s ninth and final book.',
        'book_type' => 'online',
        'total_quantity' => 1,
        'rental_price_per_day' => null,
        'purchase_price' => 9.99,
        'publisher' => 'Signet Classic',
        'publication_date' => '1949-06-08',
        'language' => 'English',
        'pages' => 328
    ],
    [
        'title' => 'Pride and Prejudice',
        'author' => 'Jane Austen',
        'isbn' => '978-0141439518',
        'genre' => 'Romance',
        'description' => 'Pride and Prejudice is a romantic novel of manners written by Jane Austen in 1813. The novel follows the character development of Elizabeth Bennet.',
        'book_type' => 'physical',
        'total_quantity' => 6,
        'rental_price_per_day' => 2.00,
        'purchase_price' => null,
        'publisher' => 'Penguin Classics',
        'publication_date' => '1813-01-28',
        'language' => 'English',
        'pages' => 432
    ],
    [
        'title' => 'The Catcher in the Rye',
        'author' => 'J.D. Salinger',
        'isbn' => '978-0316769488',
        'genre' => 'Fiction',
        'description' => 'The Catcher in the Rye is a novel by J. D. Salinger, partially published in serial form in 1945-1946 and as a novel in 1951. It is a classic coming-of-age story.',
        'book_type' => 'physical',
        'total_quantity' => 4,
        'rental_price_per_day' => 2.75,
        'purchase_price' => null,
        'publisher' => 'Little, Brown and Company',
        'publication_date' => '1951-07-16',
        'language' => 'English',
        'pages' => 277
    ],
    [
        'title' => 'The Hobbit',
        'author' => 'J.R.R. Tolkien',
        'isbn' => '978-0547928227',
        'genre' => 'Fantasy',
        'description' => 'The Hobbit is a children\'s fantasy novel by English author J. R. R. Tolkien. It follows the quest of home-loving Bilbo Baggins to win a share of the treasure guarded by Smaug the dragon.',
        'book_type' => 'online',
        'total_quantity' => 1,
        'rental_price_per_day' => null,
        'purchase_price' => 12.99,
        'publisher' => 'Houghton Mifflin Harcourt',
        'publication_date' => '1937-09-21',
        'language' => 'English',
        'pages' => 310
    ],
    [
        'title' => 'Harry Potter and the Sorcerer\'s Stone',
        'author' => 'J.K. Rowling',
        'isbn' => '978-0590353427',
        'genre' => 'Fantasy',
        'description' => 'Harry Potter and the Sorcerer\'s Stone is a fantasy novel written by British author J. K. Rowling. The first novel in the Harry Potter series.',
        'book_type' => 'physical',
        'total_quantity' => 10,
        'rental_price_per_day' => 3.50,
        'purchase_price' => null,
        'publisher' => 'Scholastic',
        'publication_date' => '1997-06-26',
        'language' => 'English',
        'pages' => 309
    ],
    [
        'title' => 'The Lord of the Rings',
        'author' => 'J.R.R. Tolkien',
        'isbn' => '978-0618640157',
        'genre' => 'Fantasy',
        'description' => 'The Lord of the Rings is an epic high-fantasy novel by English author J. R. R. Tolkien. The story began as a sequel to Tolkien\'s 1937 fantasy novel The Hobbit.',
        'book_type' => 'physical',
        'total_quantity' => 7,
        'rental_price_per_day' => 4.00,
        'purchase_price' => null,
        'publisher' => 'Mariner Books',
        'publication_date' => '1954-07-29',
        'language' => 'English',
        'pages' => 1178
    ],
    [
        'title' => 'The Da Vinci Code',
        'author' => 'Dan Brown',
        'isbn' => '978-0307474278',
        'genre' => 'Thriller',
        'description' => 'The Da Vinci Code is a 2003 mystery thriller novel by Dan Brown. It is Brown\'s second novel to include the character Robert Langdon.',
        'book_type' => 'online',
        'total_quantity' => 1,
        'rental_price_per_day' => null,
        'purchase_price' => 8.99,
        'publisher' => 'Anchor',
        'publication_date' => '2003-03-18',
        'language' => 'English',
        'pages' => 489
    ],
    [
        'title' => 'The Alchemist',
        'author' => 'Paulo Coelho',
        'isbn' => '978-0062315007',
        'genre' => 'Fiction',
        'description' => 'The Alchemist is a novel by Brazilian author Paulo Coelho that was first published in 1988. A philosophical tale about a young Andalusian shepherd who dreams of finding treasure.',
        'book_type' => 'physical',
        'total_quantity' => 9,
        'rental_price_per_day' => 2.25,
        'purchase_price' => null,
        'publisher' => 'HarperOne',
        'publication_date' => '1988-01-01',
        'language' => 'English',
        'pages' => 208
    ]
];

// Function to create a simple colored placeholder image using pure PHP (no GD required)
function createPlaceholderSVG($title, $author, $genre) {
    // Color mapping for genres
    $colors = [
        'Fiction' => '#4682B4',
        'Romance' => '#DC143C',
        'Fantasy' => '#4B0082',
        'Dystopian' => '#2F4F4F',
        'Thriller' => '#191970',
    ];
    
    $bgColor = $colors[$genre] ?? '#228B22';
    
    // Escape and truncate text for SVG
    $titleLines = wordwrap($title, 18, "\n", true);
    $titleParts = explode("\n", $titleLines);
    $authorEsc = htmlspecialchars(substr($author, 0, 25));
    $genreEsc = htmlspecialchars(strtoupper($genre));
    
    // Build title text elements
    $titleY = 160;
    $titleElements = '';
    foreach ($titleParts as $i => $part) {
        $partEsc = htmlspecialchars($part);
        $y = $titleY + ($i * 25);
        $titleElements .= "<text x=\"150\" y=\"$y\" font-family=\"Georgia, serif\" font-size=\"16\" fill=\"white\" text-anchor=\"middle\" font-weight=\"bold\">$partEsc</text>\n";
    }
    
    // Create SVG
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="300" height="450" viewBox="0 0 300 450">
  <defs>
    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:$bgColor;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#000000;stop-opacity:0.3" />
    </linearGradient>
  </defs>
  <rect width="300" height="450" fill="url(#bg)"/>
  <rect x="8" y="8" width="284" height="434" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="1"/>
  <rect x="15" y="15" width="270" height="420" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>
  <rect x="15" y="15" width="270" height="45" fill="rgba(0,0,0,0.4)"/>
  <text x="150" y="45" font-family="Arial, sans-serif" font-size="12" fill="rgba(255,255,255,0.9)" text-anchor="middle" letter-spacing="3">$genreEsc</text>
  <line x1="40" y1="75" x2="260" y2="75" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>
  $titleElements
  <line x1="40" y1="320" x2="260" y2="320" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>
  <text x="150" y="355" font-family="Georgia, serif" font-size="13" fill="rgba(255,255,255,0.8)" text-anchor="middle" font-style="italic">by $authorEsc</text>
  <rect x="15" y="395" width="270" height="40" fill="rgba(0,0,0,0.3)"/>
  <text x="150" y="420" font-family="Arial, sans-serif" font-size="11" fill="rgba(255,255,255,0.6)" text-anchor="middle" letter-spacing="2">BOOK HUB</text>
</svg>
SVG;
    
    return $svg;
}

echo "<html><head><title>Add Sample Books</title>";
echo "<style>
    body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
    .success { color: #155724; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .error { color: #721c24; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }
    .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; }
    h1 { color: #333; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
    th { background: #4CAF50; color: white; }
    tr:nth-child(even) { background: #f9f9f9; }
    tr:hover { background: #f1f1f1; }
    .btn { display: inline-block; padding: 12px 24px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; margin-right: 10px; }
    .btn:hover { background: #45a049; }
    .btn-blue { background: #2196F3; }
    .btn-blue:hover { background: #1976D2; }
</style></head><body>";

echo "<h1>📚 BookHub - Add Sample Books</h1>";

// Check if books already exist
$checkQuery = "SELECT COUNT(*) as count FROM books";
$result = $conn->query($checkQuery);
$existingCount = $result->fetch_assoc()['count'];

if ($existingCount > 0) {
    echo "<div class='info'>ℹ️ There are already <strong>$existingCount books</strong> in the database.</div>";
}

$addedCount = 0;
$errors = [];

echo "<h2>Adding 10 Sample Books...</h2>";
echo "<table>";
echo "<tr><th>#</th><th>Title</th><th>Author</th><th>Type</th><th>Price</th><th>Status</th></tr>";

foreach ($books as $index => $book) {
    // Check if book already exists (by ISBN)
    $checkSql = "SELECT id FROM books WHERE isbn = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $book['isbn']);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $price = $book['book_type'] === 'physical' ? '$' . number_format($book['rental_price_per_day'], 2) . '/day' : '$' . number_format($book['purchase_price'], 2);
        echo "<tr><td>" . ($index + 1) . "</td><td>{$book['title']}</td><td>{$book['author']}</td><td>{$book['book_type']}</td><td>$price</td><td>⚠️ Already exists (skipped)</td></tr>";
        continue;
    }
    
    // Create SVG placeholder image
    $coverImage = createPlaceholderSVG($book['title'], $book['author'], $book['genre']);
    $coverImageType = 'image/svg+xml';
    
    // Prepare SQL
    $sql = "INSERT INTO books (title, author, isbn, genre, description, book_type, total_quantity, available_quantity, rental_price_per_day, purchase_price, publisher, publication_date, language, pages, cover_image, cover_image_type, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        $errors[] = "Failed to prepare statement for '{$book['title']}': " . $conn->error;
        echo "<tr><td>" . ($index + 1) . "</td><td>{$book['title']}</td><td>{$book['author']}</td><td>{$book['book_type']}</td><td>-</td><td>❌ Prepare Error</td></tr>";
        continue;
    }
    
    $availableQuantity = $book['total_quantity'];
    $rentalPrice = $book['rental_price_per_day'];
    $purchasePrice = $book['purchase_price'];
    
    $stmt->bind_param(
        "ssssssiiddssisis",
        $book['title'],
        $book['author'],
        $book['isbn'],
        $book['genre'],
        $book['description'],
        $book['book_type'],
        $book['total_quantity'],
        $availableQuantity,
        $rentalPrice,
        $purchasePrice,
        $book['publisher'],
        $book['publication_date'],
        $book['language'],
        $book['pages'],
        $coverImage,
        $coverImageType
    );
    
    if ($stmt->execute()) {
        $addedCount++;
        $bookType = $book['book_type'] === 'physical' ? '📖 Physical' : '💻 Online';
        $price = $book['book_type'] === 'physical' ? '$' . number_format($book['rental_price_per_day'], 2) . '/day' : '$' . number_format($book['purchase_price'], 2);
        echo "<tr><td>" . ($index + 1) . "</td><td><strong>{$book['title']}</strong></td><td>{$book['author']}</td><td>$bookType</td><td>$price</td><td>✅ Added successfully</td></tr>";
    } else {
        $errors[] = "Failed to add '{$book['title']}': " . $stmt->error;
        echo "<tr><td>" . ($index + 1) . "</td><td>{$book['title']}</td><td>{$book['author']}</td><td>{$book['book_type']}</td><td>-</td><td>❌ " . htmlspecialchars($stmt->error) . "</td></tr>";
    }
    
    $stmt->close();
}

echo "</table>";

// Summary
echo "<h2>📊 Summary</h2>";
if ($addedCount > 0) {
    echo "<div class='success'>✅ Successfully added <strong>$addedCount</strong> new book(s) to the database with cover images!</div>";
} else {
    echo "<div class='info'>ℹ️ No new books were added (they may already exist in the database).</div>";
}

if (!empty($errors)) {
    echo "<div class='error'>";
    echo "<strong>⚠️ Errors encountered:</strong><br>";
    foreach ($errors as $error) {
        echo "• " . htmlspecialchars($error) . "<br>";
    }
    echo "</div>";
}

// Get final count
$finalQuery = "SELECT COUNT(*) as count FROM books";
$finalResult = $conn->query($finalQuery);
$finalCount = $finalResult->fetch_assoc()['count'];
echo "<div class='info'>📚 Total books in database: <strong>$finalCount</strong></div>";

echo "<br><a href='/book-hub/src/views/manage-books.php' class='btn'>📋 Manage Books</a>";
echo "<a href='/book-hub/public/books.php' class='btn btn-blue'>📖 View Books Catalog</a>";

echo "</body></html>";

$conn->close();
?>
