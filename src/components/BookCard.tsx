import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Star, ShoppingCart, BookOpen } from "lucide-react";

interface BookCardProps {
  title: string;
  author: string;
  image: string;
  rating: number;
  price?: number;
  rentalFee?: number;
  type: "rent" | "buy";
  isAvailable?: boolean;
}

const BookCard = ({
  title,
  author,
  image,
  rating,
  price,
  rentalFee,
  type,
  isAvailable = true,
}: BookCardProps) => {
  return (
    <Card className="group overflow-hidden transition-all duration-300 hover:shadow-xl border-border">
      <div className="relative overflow-hidden aspect-[3/4]">
        <img
          src={image}
          alt={title}
          className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
        />
        <div className="absolute top-3 right-3">
          <span
            className={`px-3 py-1 rounded-full text-xs font-semibold ${
              type === "rent"
                ? "bg-accent text-accent-foreground"
                : "bg-secondary text-secondary-foreground"
            }`}
          >
            {type === "rent" ? "For Rent" : "Buy Now"}
          </span>
        </div>
        {!isAvailable && (
          <div className="absolute inset-0 bg-primary/80 flex items-center justify-center">
            <span className="text-primary-foreground font-bold text-lg">Not Available</span>
          </div>
        )}
      </div>

      <CardContent className="p-4">
        <h3 className="font-bold text-lg mb-1 line-clamp-1 text-foreground">{title}</h3>
        <p className="text-muted-foreground text-sm mb-3">{author}</p>

        <div className="flex items-center gap-1 mb-3">
          {[...Array(5)].map((_, i) => (
            <Star
              key={i}
              className={`h-4 w-4 ${
                i < rating ? "fill-accent text-accent" : "fill-muted text-muted"
              }`}
            />
          ))}
          <span className="text-sm text-muted-foreground ml-1">({rating}.0)</span>
        </div>

        <div className="flex items-center justify-between">
          <div>
            <p className="text-2xl font-bold text-primary">
              ${type === "rent" ? rentalFee : price}
              {type === "rent" && <span className="text-sm font-normal text-muted-foreground">/week</span>}
            </p>
          </div>
          <Button
            size="sm"
            className={
              type === "rent"
                ? "bg-accent hover:bg-accent/90"
                : "bg-secondary hover:bg-secondary/90"
            }
            disabled={!isAvailable}
          >
            {type === "rent" ? (
              <>
                <BookOpen className="h-4 w-4 mr-1" />
                Rent
              </>
            ) : (
              <>
                <ShoppingCart className="h-4 w-4 mr-1" />
                Buy
              </>
            )}
          </Button>
        </div>
      </CardContent>
    </Card>
  );
};

export default BookCard;
