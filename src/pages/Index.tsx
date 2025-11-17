import { Button } from "@/components/ui/button";
import { Search, BookOpen, Download, DollarSign, Users, Star, TrendingUp } from "lucide-react";
import BookCard from "@/components/BookCard";
import Navigation from "@/components/Navigation";
import Footer from "@/components/Footer";
import heroImage from "@/assets/hero-library.jpg";
import book1 from "@/assets/book-1.jpg";
import book2 from "@/assets/book-2.jpg";
import book3 from "@/assets/book-3.jpg";
import book4 from "@/assets/book-4.jpg";

const Index = () => {
  const featuredBooks = [
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
  ];

  const stats = [
    { icon: BookOpen, value: "10,000+", label: "Books Available" },
    { icon: Users, value: "5,000+", label: "Happy Readers" },
    { icon: Download, value: "3,000+", label: "Digital Titles" },
    { icon: Star, value: "4.9/5", label: "Average Rating" },
  ];

  const benefits = [
    {
      icon: BookOpen,
      title: "Vast Collection",
      description: "Access thousands of books across all genres and categories",
    },
    {
      icon: TrendingUp,
      title: "Easy Rental",
      description: "Simple rental process with flexible periods and affordable rates",
    },
    {
      icon: Download,
      title: "Instant Downloads",
      description: "Buy digital books and download them instantly in PDF format",
    },
    {
      icon: DollarSign,
      title: "Affordable Prices",
      description: "Competitive pricing for both rentals and digital purchases",
    },
  ];

  return (
    <div className="min-h-screen">
      <Navigation />

      {/* Hero Section */}
      <section className="relative h-screen flex items-center justify-center overflow-hidden">
        <div
          className="absolute inset-0 z-0"
          style={{
            backgroundImage: `url(${heroImage})`,
            backgroundSize: "cover",
            backgroundPosition: "center",
          }}
        >
          <div className="absolute inset-0 bg-primary/70 backdrop-blur-sm" />
        </div>

        <div className="relative z-10 container mx-auto px-4 text-center text-primary-foreground">
          <h1 className="text-5xl md:text-7xl font-bold mb-6 animate-in fade-in slide-in-from-bottom-4 duration-1000">
            Discover Your Next Great Read
          </h1>
          <p className="text-xl md:text-2xl mb-8 text-primary-foreground/90 max-w-2xl mx-auto animate-in fade-in slide-in-from-bottom-4 duration-1000 delay-200">
            Rent physical books or buy digital editions instantly
          </p>

          <div className="flex flex-col sm:flex-row gap-4 justify-center mb-12 animate-in fade-in slide-in-from-bottom-4 duration-1000 delay-300">
            <Button size="lg" className="bg-secondary hover:bg-secondary/90 text-lg px-8">
              Browse Books
            </Button>
            <Button
              size="lg"
              variant="outline"
              className="text-lg px-8 border-primary-foreground text-primary-foreground hover:bg-primary-foreground hover:text-primary"
            >
              Get Started
            </Button>
          </div>

          {/* Search Bar */}
          <div className="max-w-2xl mx-auto animate-in fade-in slide-in-from-bottom-4 duration-1000 delay-500">
            <div className="flex gap-2 glass-morphism rounded-full p-2">
              <input
                type="text"
                placeholder="Search for books, authors, or genres..."
                className="flex-1 bg-transparent border-none outline-none px-6 text-primary-foreground placeholder:text-primary-foreground/60"
              />
              <Button size="lg" className="bg-secondary hover:bg-secondary/90 rounded-full px-8">
                <Search className="h-5 w-5 mr-2" />
                Search
              </Button>
            </div>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="py-16 bg-card">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-8">
            {stats.map((stat, index) => (
              <div key={index} className="text-center">
                <stat.icon className="h-12 w-12 mx-auto mb-4 text-secondary" />
                <h3 className="text-3xl font-bold text-primary mb-2">{stat.value}</h3>
                <p className="text-muted-foreground">{stat.label}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Featured Books */}
      <section className="py-20 bg-background">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold text-primary mb-4">Featured Books</h2>
            <p className="text-muted-foreground text-lg">
              Discover our handpicked selection of must-read titles
            </p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {featuredBooks.map((book, index) => (
              <BookCard key={index} {...book} />
            ))}
          </div>

          <div className="text-center">
            <Button size="lg" className="bg-primary hover:bg-primary/90">
              View All Books
            </Button>
          </div>
        </div>
      </section>

      {/* Why Choose Us */}
      <section className="py-20 bg-muted">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold text-primary mb-4">Why Choose BOOK HUB?</h2>
            <p className="text-muted-foreground text-lg">
              Experience the best in book rental and digital purchases
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {benefits.map((benefit, index) => (
              <div
                key={index}
                className="bg-card p-6 rounded-xl text-center transition-all duration-300 hover:shadow-xl hover:-translate-y-2"
              >
                <div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-secondary/10 mb-4">
                  <benefit.icon className="h-8 w-8 text-secondary" />
                </div>
                <h3 className="text-xl font-bold text-foreground mb-3">{benefit.title}</h3>
                <p className="text-muted-foreground">{benefit.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 bg-primary text-primary-foreground">
        <div className="container mx-auto px-4 text-center">
          <h2 className="text-4xl font-bold mb-6">Ready to Start Your Reading Journey?</h2>
          <p className="text-xl mb-8 text-primary-foreground/90 max-w-2xl mx-auto">
            Join thousands of readers and get access to our vast collection of books today
          </p>
          <Button size="lg" className="bg-secondary hover:bg-secondary/90 text-lg px-8">
            Sign Up Now
          </Button>
        </div>
      </section>

      <Footer />
    </div>
  );
};

export default Index;
