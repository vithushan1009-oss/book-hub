import { useState } from "react";
import { Button } from "@/components/ui/button";
import Navigation from "@/components/Navigation";
import Footer from "@/components/Footer";
import book1 from "@/assets/book-1.jpg";
import book2 from "@/assets/book-2.jpg";
import book3 from "@/assets/book-3.jpg";
import book4 from "@/assets/book-4.jpg";
import heroImage from "@/assets/hero-library.jpg";
import aboutTeam from "@/assets/about-team.jpg";

const Gallery = () => {
  const [selectedCategory, setSelectedCategory] = useState("all");

  const categories = ["all", "library", "books", "events"];

  const images = [
    { src: heroImage, category: "library", title: "Modern Reading Space" },
    { src: book1, category: "books", title: "Fiction Collection" },
    { src: book2, category: "books", title: "Non-Fiction Selection" },
    { src: book3, category: "books", title: "Fantasy Novels" },
    { src: book4, category: "books", title: "Science & Education" },
    { src: aboutTeam, category: "events", title: "Community Reading Event" },
    { src: heroImage, category: "library", title: "Cozy Reading Corner" },
    { src: book1, category: "books", title: "Latest Arrivals" },
  ];

  const filteredImages =
    selectedCategory === "all"
      ? images
      : images.filter((img) => img.category === selectedCategory);

  return (
    <div className="min-h-screen bg-background">
      <Navigation />

      {/* Page Header */}
      <section className="pt-32 pb-16 bg-gradient-to-b from-muted to-background">
        <div className="container mx-auto px-4">
          <div className="text-center max-w-3xl mx-auto">
            <h1 className="text-5xl font-bold text-primary mb-6">Gallery</h1>
            <p className="text-muted-foreground text-lg">
              Explore our beautiful library spaces, book collections, and community events
            </p>
          </div>
        </div>
      </section>

      {/* Category Filter */}
      <section className="py-8">
        <div className="container mx-auto px-4">
          <div className="flex flex-wrap justify-center gap-3">
            {categories.map((category) => (
              <Button
                key={category}
                variant={selectedCategory === category ? "default" : "outline"}
                onClick={() => setSelectedCategory(category)}
                className={
                  selectedCategory === category
                    ? "bg-secondary hover:bg-secondary/90"
                    : ""
                }
              >
                {category.charAt(0).toUpperCase() + category.slice(1)}
              </Button>
            ))}
          </div>
        </div>
      </section>

      {/* Gallery Grid */}
      <section className="py-12 pb-20">
        <div className="container mx-auto px-4">
          <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredImages.map((image, index) => (
              <div
                key={index}
                className="group relative overflow-hidden rounded-xl shadow-lg aspect-square cursor-pointer"
              >
                <img
                  src={image.src}
                  alt={image.title}
                  className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end">
                  <div className="p-6 text-primary-foreground">
                    <h3 className="text-xl font-bold">{image.title}</h3>
                    <p className="text-sm text-primary-foreground/80 capitalize">
                      {image.category}
                    </p>
                  </div>
                </div>
              </div>
            ))}
          </div>

          {filteredImages.length === 0 && (
            <div className="text-center py-20">
              <p className="text-muted-foreground text-lg">
                No images found in this category
              </p>
            </div>
          )}
        </div>
      </section>

      <Footer />
    </div>
  );
};

export default Gallery;
