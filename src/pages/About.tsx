import Navigation from "@/components/Navigation";
import Footer from "@/components/Footer";
import { BookOpen, Target, Eye, Award, Users } from "lucide-react";
import aboutTeam from "@/assets/about-team.jpg";

const About = () => {
  const values = [
    {
      icon: BookOpen,
      title: "Love for Reading",
      description: "We believe reading enriches lives and broadens perspectives",
    },
    {
      icon: Users,
      title: "Community Focus",
      description: "Building a community of passionate readers and book enthusiasts",
    },
    {
      icon: Award,
      title: "Quality Service",
      description: "Committed to providing exceptional service and book selections",
    },
    {
      icon: Target,
      title: "Accessibility",
      description: "Making books accessible to everyone through flexible options",
    },
  ];

  const milestones = [
    { year: "2020", event: "BOOK HUB Founded", description: "Started with 1,000 books" },
    { year: "2021", event: "Digital Platform Launch", description: "Introduced digital book purchases" },
    { year: "2023", event: "10,000+ Books", description: "Expanded collection to over 10,000 titles" },
    { year: "2025", event: "5,000+ Members", description: "Growing community of happy readers" },
  ];

  return (
    <div className="min-h-screen bg-background">
      <Navigation />

      {/* Hero Section */}
      <section className="pt-32 pb-16 bg-gradient-to-b from-muted to-background">
        <div className="container mx-auto px-4">
          <div className="text-center max-w-3xl mx-auto">
            <h1 className="text-5xl font-bold text-primary mb-6">About BOOK HUB</h1>
            <p className="text-muted-foreground text-lg leading-relaxed">
              We're passionate about connecting readers with the stories they love. Since 2020,
              BOOK HUB has been your trusted partner in discovering, renting, and purchasing books.
            </p>
          </div>
        </div>
      </section>

      {/* Mission & Vision */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="grid md:grid-cols-2 gap-12">
            <div className="bg-card p-8 rounded-2xl shadow-lg">
              <div className="flex items-center gap-3 mb-4">
                <Target className="h-10 w-10 text-secondary" />
                <h2 className="text-3xl font-bold text-primary">Our Mission</h2>
              </div>
              <p className="text-muted-foreground leading-relaxed">
                To make reading accessible, affordable, and enjoyable for everyone by offering a vast
                collection of physical and digital books. We strive to foster a love for reading and
                create a vibrant community of book enthusiasts.
              </p>
            </div>

            <div className="bg-card p-8 rounded-2xl shadow-lg">
              <div className="flex items-center gap-3 mb-4">
                <Eye className="h-10 w-10 text-accent" />
                <h2 className="text-3xl font-bold text-primary">Our Vision</h2>
              </div>
              <p className="text-muted-foreground leading-relaxed">
                To become the world's most trusted and innovative library management platform,
                bridging the gap between traditional book rentals and modern digital publishing,
                while nurturing the timeless joy of reading.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Team Section */}
      <section className="py-20 bg-muted">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold text-primary mb-4">Meet Our Team</h2>
            <p className="text-muted-foreground text-lg">
              Passionate book lovers dedicated to serving our community
            </p>
          </div>

          <div className="max-w-4xl mx-auto">
            <img
              src={aboutTeam}
              alt="BOOK HUB Team"
              className="w-full rounded-2xl shadow-2xl"
            />
          </div>

          <div className="text-center mt-8 max-w-2xl mx-auto">
            <p className="text-muted-foreground leading-relaxed">
              Our diverse team of librarians, technologists, and book enthusiasts work together
              to curate the best reading experience for you. With decades of combined experience
              in library management and digital publishing, we're here to help you discover your
              next great read.
            </p>
          </div>
        </div>
      </section>

      {/* Values */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold text-primary mb-4">Our Values</h2>
            <p className="text-muted-foreground text-lg">
              The principles that guide everything we do
            </p>
          </div>

          <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            {values.map((value, index) => (
              <div
                key={index}
                className="bg-card p-6 rounded-xl text-center transition-all duration-300 hover:shadow-xl hover:-translate-y-2"
              >
                <div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-secondary/10 mb-4">
                  <value.icon className="h-8 w-8 text-secondary" />
                </div>
                <h3 className="text-xl font-bold text-foreground mb-3">{value.title}</h3>
                <p className="text-muted-foreground">{value.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Timeline */}
      <section className="py-20 bg-muted">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold text-primary mb-4">Our Journey</h2>
            <p className="text-muted-foreground text-lg">
              Key milestones in our growth story
            </p>
          </div>

          <div className="max-w-4xl mx-auto">
            <div className="space-y-8">
              {milestones.map((milestone, index) => (
                <div
                  key={index}
                  className="flex gap-6 items-start bg-card p-6 rounded-xl shadow-md"
                >
                  <div className="flex-shrink-0">
                    <div className="w-20 h-20 rounded-full bg-secondary flex items-center justify-center">
                      <span className="text-secondary-foreground font-bold text-lg">
                        {milestone.year}
                      </span>
                    </div>
                  </div>
                  <div className="flex-1">
                    <h3 className="text-2xl font-bold text-primary mb-2">{milestone.event}</h3>
                    <p className="text-muted-foreground">{milestone.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      <Footer />
    </div>
  );
};

export default About;
