import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Search, SlidersHorizontal } from "lucide-react";
import BookCard from "@/components/BookCard";
import Navigation from "@/components/Navigation";
import Footer from "@/components/Footer";
import book1 from "@/assets/book-1.jpg";
import book2 from "@/assets/book-2.jpg";
import book3 from "@/assets/book-3.jpg";
import book4 from "@/assets/book-4.jpg";

const Books = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const [category, setCategory] = useState("all");
  const [sortBy, setSortBy] = useState("popular");

  const books = [
    {
      title: "Fiction Fomen",
      author: "Shen Gerdings",
      image: book1,
      rating: 5,
      price: 12.99,
      type: "buy" as const,
    },
    {
      title: "Nook",
      author: "Bab Giuing",
      image: book2,
      rating: 4,
      rentalFee: 3.99,
      type: "rent" as const,
    },
    {
      title: "Mystic Tales",
      author: "Fantasy Author",
      image: book3,
      rating: 5,
      price: 15.99,
      type: "buy" as const,
    },
    {
      title: "Science Wonders",
      author: "Knowledge Seeker",
      image: book4,
      rating: 4,
      rentalFee: 4.99,
      type: "rent" as const,
    },
    {
      title: "Fiction Fomen",
      author: "Shen Gerdings",
      image: book1,
      rating: 5,
      price: 12.99,
      type: "buy" as const,
    },
    {
      title: "Nook",
      author: "Bab Giuing",
      image: book2,
      rating: 4,
      rentalFee: 3.99,
      type: "rent" as const,
      isAvailable: false,
    },
    {
      title: "Mystic Tales",
      author: "Fantasy Author",
      image: book3,
      rating: 5,
      price: 15.99,
      type: "buy" as const,
    },
    {
      title: "Science Wonders",
      author: "Knowledge Seeker",
      image: book4,
      rating: 4,
      rentalFee: 4.99,
      type: "rent" as const,
    },
  ];

  return (
    <div className="min-h-screen bg-background">
      <Navigation />

      {/* Page Header */}
      <section className="pt-32 pb-12 bg-gradient-to-b from-muted to-background">
        <div className="container mx-auto px-4">
          <h1 className="text-5xl font-bold text-primary mb-4 text-center">Our Book Collection</h1>
          <p className="text-muted-foreground text-lg text-center max-w-2xl mx-auto">
            Explore thousands of books available for rent or purchase
          </p>
        </div>
      </section>

      {/* Filters and Search */}
      <section className="py-8 bg-background sticky top-20 z-40 border-b border-border">
        <div className="container mx-auto px-4">
          <div className="flex flex-col lg:flex-row gap-4">
            {/* Search */}
            <div className="flex-1 relative">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
              <Input
                type="text"
                placeholder="Search books, authors, ISBN..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="pl-10"
              />
            </div>

            {/* Category Filter */}
            <Select value={category} onValueChange={setCategory}>
              <SelectTrigger className="w-full lg:w-48">
                <SelectValue placeholder="Category" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All Categories</SelectItem>
                <SelectItem value="fiction">Fiction</SelectItem>
                <SelectItem value="non-fiction">Non-Fiction</SelectItem>
                <SelectItem value="fantasy">Fantasy</SelectItem>
                <SelectItem value="science">Science</SelectItem>
                <SelectItem value="biography">Biography</SelectItem>
              </SelectContent>
            </Select>

            {/* Sort */}
            <Select value={sortBy} onValueChange={setSortBy}>
              <SelectTrigger className="w-full lg:w-48">
                <SelectValue placeholder="Sort by" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="popular">Most Popular</SelectItem>
                <SelectItem value="newest">Newest First</SelectItem>
                <SelectItem value="price-low">Price: Low to High</SelectItem>
                <SelectItem value="price-high">Price: High to Low</SelectItem>
                <SelectItem value="rating">Highest Rated</SelectItem>
              </SelectContent>
            </Select>

            {/* Filter Button */}
            <Button variant="outline" className="lg:w-auto">
              <SlidersHorizontal className="h-5 w-5 mr-2" />
              Filters
            </Button>
          </div>
        </div>
      </section>

      {/* Books Grid */}
      <section className="py-12">
        <div className="container mx-auto px-4">
          <div className="flex justify-between items-center mb-6">
            <p className="text-muted-foreground">
              Showing <span className="font-semibold text-foreground">{books.length}</span> books
            </p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {books.map((book, index) => (
              <BookCard key={index} {...book} />
            ))}
          </div>

          {/* Pagination */}
          <div className="flex justify-center gap-2 mt-12">
            <Button variant="outline">Previous</Button>
            <Button variant="outline" className="bg-primary text-primary-foreground">
              1
            </Button>
            <Button variant="outline">2</Button>
            <Button variant="outline">3</Button>
            <Button variant="outline">Next</Button>
          </div>
        </div>
      </section>

      <Footer />
    </div>
  );
};

export default Books;
