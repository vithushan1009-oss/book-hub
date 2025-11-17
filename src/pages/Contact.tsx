import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Card, CardContent } from "@/components/ui/card";
import Navigation from "@/components/Navigation";
import Footer from "@/components/Footer";
import { Mail, Phone, MapPin, Clock } from "lucide-react";

const Contact = () => {
  return (
    <div className="min-h-screen bg-background">
      <Navigation />

      {/* Page Header */}
      <section className="pt-32 pb-16 bg-gradient-to-b from-muted to-background">
        <div className="container mx-auto px-4">
          <div className="text-center max-w-3xl mx-auto">
            <h1 className="text-5xl font-bold text-primary mb-6">Get in Touch</h1>
            <p className="text-muted-foreground text-lg">
              Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
            </p>
          </div>
        </div>
      </section>

      {/* Contact Info Cards */}
      <section className="py-12">
        <div className="container mx-auto px-4">
          <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <Card className="text-center transition-all duration-300 hover:shadow-xl">
              <CardContent className="pt-6">
                <div className="inline-flex items-center justify-center w-14 h-14 rounded-full bg-secondary/10 mb-4">
                  <Phone className="h-7 w-7 text-secondary" />
                </div>
                <h3 className="font-bold text-lg mb-2 text-foreground">Phone</h3>
                <p className="text-muted-foreground">+1 (555) 123-4567</p>
              </CardContent>
            </Card>

            <Card className="text-center transition-all duration-300 hover:shadow-xl">
              <CardContent className="pt-6">
                <div className="inline-flex items-center justify-center w-14 h-14 rounded-full bg-accent/10 mb-4">
                  <Mail className="h-7 w-7 text-accent" />
                </div>
                <h3 className="font-bold text-lg mb-2 text-foreground">Email</h3>
                <p className="text-muted-foreground">info@bookhub.com</p>
              </CardContent>
            </Card>

            <Card className="text-center transition-all duration-300 hover:shadow-xl">
              <CardContent className="pt-6">
                <div className="inline-flex items-center justify-center w-14 h-14 rounded-full bg-primary/10 mb-4">
                  <MapPin className="h-7 w-7 text-primary" />
                </div>
                <h3 className="font-bold text-lg mb-2 text-foreground">Address</h3>
                <p className="text-muted-foreground">123 Library Street, BookCity</p>
              </CardContent>
            </Card>

            <Card className="text-center transition-all duration-300 hover:shadow-xl">
              <CardContent className="pt-6">
                <div className="inline-flex items-center justify-center w-14 h-14 rounded-full bg-success/10 mb-4">
                  <Clock className="h-7 w-7 text-success" />
                </div>
                <h3 className="font-bold text-lg mb-2 text-foreground">Hours</h3>
                <p className="text-muted-foreground">Mon-Sat: 9AM-8PM</p>
              </CardContent>
            </Card>
          </div>
        </div>
      </section>

      {/* Contact Form & Map */}
      <section className="py-12 pb-20">
        <div className="container mx-auto px-4">
          <div className="grid lg:grid-cols-2 gap-12">
            {/* Contact Form */}
            <div>
              <h2 className="text-3xl font-bold text-primary mb-6">Send Us a Message</h2>
              <form className="space-y-6">
                <div className="grid sm:grid-cols-2 gap-4">
                  <div>
                    <label htmlFor="firstName" className="block text-sm font-medium mb-2 text-foreground">
                      First Name
                    </label>
                    <Input id="firstName" placeholder="John" />
                  </div>
                  <div>
                    <label htmlFor="lastName" className="block text-sm font-medium mb-2 text-foreground">
                      Last Name
                    </label>
                    <Input id="lastName" placeholder="Doe" />
                  </div>
                </div>

                <div>
                  <label htmlFor="email" className="block text-sm font-medium mb-2 text-foreground">
                    Email Address
                  </label>
                  <Input id="email" type="email" placeholder="john@example.com" />
                </div>

                <div>
                  <label htmlFor="subject" className="block text-sm font-medium mb-2 text-foreground">
                    Subject
                  </label>
                  <Input id="subject" placeholder="How can we help you?" />
                </div>

                <div>
                  <label htmlFor="message" className="block text-sm font-medium mb-2 text-foreground">
                    Message
                  </label>
                  <Textarea
                    id="message"
                    placeholder="Tell us more about your inquiry..."
                    rows={6}
                  />
                </div>

                <Button size="lg" className="w-full bg-secondary hover:bg-secondary/90">
                  Send Message
                </Button>
              </form>
            </div>

            {/* Map */}
            <div>
              <h2 className="text-3xl font-bold text-primary mb-6">Visit Our Location</h2>
              <div className="rounded-xl overflow-hidden shadow-lg h-[500px] bg-muted">
                <iframe
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2sNew%20York%20Public%20Library!5e0!3m2!1sen!2sus!4v1234567890123"
                  width="100%"
                  height="100%"
                  style={{ border: 0 }}
                  allowFullScreen
                  loading="lazy"
                  referrerPolicy="no-referrer-when-downgrade"
                  title="BOOK HUB Location"
                />
              </div>

              <Card className="mt-6">
                <CardContent className="pt-6">
                  <h3 className="font-bold text-xl mb-4 text-primary">Opening Hours</h3>
                  <div className="space-y-2 text-muted-foreground">
                    <div className="flex justify-between">
                      <span>Monday - Friday</span>
                      <span className="font-semibold text-foreground">9:00 AM - 8:00 PM</span>
                    </div>
                    <div className="flex justify-between">
                      <span>Saturday</span>
                      <span className="font-semibold text-foreground">10:00 AM - 6:00 PM</span>
                    </div>
                    <div className="flex justify-between">
                      <span>Sunday</span>
                      <span className="font-semibold text-foreground">Closed</span>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </section>

      <Footer />
    </div>
  );
};

export default Contact;
