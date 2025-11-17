import { useState, useEffect } from "react";
import { NavLink } from "@/components/NavLink";
import { Button } from "@/components/ui/button";
import { BookOpen, Menu, X } from "lucide-react";

const Navigation = () => {
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 50);
    };

    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const navLinks = [
    { to: "/", label: "Home" },
    { to: "/books", label: "Books" },
    { to: "/about", label: "About Us" },
    { to: "/gallery", label: "Gallery" },
    { to: "/contact", label: "Contact" },
  ];

  return (
    <nav
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        isScrolled ? "glass-morphism shadow-lg" : "bg-transparent"
      }`}
    >
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between h-20">
          {/* Logo */}
          <NavLink to="/" className="flex items-center gap-2 group">
            <BookOpen className="h-8 w-8 text-secondary transition-transform group-hover:scale-110" />
            <span className="text-2xl font-bold text-primary">
              BOOK <span className="text-secondary">HUB</span>
            </span>
          </NavLink>

          {/* Desktop Navigation */}
          <div className="hidden md:flex items-center gap-8">
            {navLinks.map((link) => (
              <NavLink
                key={link.to}
                to={link.to}
                className="text-foreground/80 hover:text-primary font-medium transition-colors relative group"
                activeClassName="text-primary"
              >
                {link.label}
                <span className="absolute bottom-0 left-0 w-0 h-0.5 bg-secondary transition-all group-hover:w-full" />
              </NavLink>
            ))}
          </div>

          {/* Desktop CTA Buttons */}
          <div className="hidden md:flex items-center gap-4">
            <Button variant="outline" className="border-primary text-primary hover:bg-primary hover:text-primary-foreground">
              Sign In
            </Button>
            <Button className="bg-secondary hover:bg-secondary/90">
              Get Started
            </Button>
          </div>

          {/* Mobile Menu Button */}
          <button
            className="md:hidden text-primary"
            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
          >
            {isMobileMenuOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
          </button>
        </div>

        {/* Mobile Menu */}
        {isMobileMenuOpen && (
          <div className="md:hidden pb-6 space-y-4">
            {navLinks.map((link) => (
              <NavLink
                key={link.to}
                to={link.to}
                className="block text-foreground/80 hover:text-primary font-medium py-2"
                activeClassName="text-primary"
                onClick={() => setIsMobileMenuOpen(false)}
              >
                {link.label}
              </NavLink>
            ))}
            <div className="flex flex-col gap-2 pt-4">
              <Button variant="outline" className="w-full border-primary text-primary">
                Sign In
              </Button>
              <Button className="w-full bg-secondary hover:bg-secondary/90">
                Get Started
              </Button>
            </div>
          </div>
        )}
      </div>
    </nav>
  );
};

export default Navigation;
