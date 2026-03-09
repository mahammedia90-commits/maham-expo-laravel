export interface Event {
  id: string;
  name: string;
  name_ar: string;
  description: string;
  description_ar: string;
  category: { id: string; name: string; name_ar?: string };
  city: { id: string; name: string; name_ar?: string };
  address: string;
  address_ar: string;
  start_date: string;
  end_date: string;
  opening_time: string;
  closing_time: string;
  images: string[];
  images_360?: string[];
  features?: string;
  features_ar?: string;
  organizer_name?: string;
  organizer_phone?: string;
  organizer_email?: string;
  website?: string;
  latitude?: number | string;
  longitude?: number | string;
  status: 'draft' | 'published' | 'ended' | 'cancelled';
  is_featured: boolean;
  views_count: number;
  expected_visitors?: number;
  available_spaces_count: number;
  total_spaces_count: number;
  min_price: number;
  created_at?: string;
}

export interface Space {
  id: string;
  event_id: string;
  name: string;
  name_ar: string;
  description: string;
  description_ar: string;
  location_code: string;
  area_sqm: number;
  price_per_day: number;
  price_total: number;
  images: string[];
  images_360?: string[];
  amenities: string[];
  amenities_ar: string[];
  status: 'available' | 'reserved' | 'rented' | 'unavailable';
  floor_number: number;
  section: { id: string; name: string };
  space_type: string;
  payment_system: string;
  rental_duration: string;
}

export interface Category {
  id: string;
  name: string;
  name_ar: string;
  icon: string;
  description: string;
  description_ar: string;
  is_active: boolean;
  sort_order: number;
}

export interface City {
  id: string;
  name: string;
  name_ar: string;
  region: string;
  region_ar: string;
  is_active: boolean;
}

export interface Invoice {
  id: string;
  invoice_number: string;
  title: string;
  title_ar: string;
  subtotal: number;
  tax_amount: number;
  discount_amount: number;
  total_amount: number;
  paid_amount: number;
  status: string;
  issue_date: string;
  due_date: string;
  paid_at: string | null;
}

export interface SupportTicket {
  id: string;
  subject: string;
  subject_ar: string;
  description: string;
  description_ar: string;
  category: string;
  priority: string;
  status: string;
  created_at: string;
  user?: { id: string; name: string; email: string };
}

export interface RentalRequest {
  id: string;
  request_number: string;
  space_id: string;
  start_date: string;
  end_date: string;
  status: string;
  status_label: string;
  notes: string;
  total_price: number;
  created_at: string;
  business_profile?: { id: string; company_name: string; company_name_ar?: string };
  space?: { id: string; name: string; name_ar: string };
}

export interface VisitRequest {
  id: string;
  event_id: string;
  visit_date: string;
  visit_time: string;
  visitors_count: number;
  status: string;
  notes: string;
  created_at: string;
  user?: { id: string; name: string };
  event?: { id: string; name: string; name_ar: string };
}

export interface Sponsor {
  id: string;
  event_id?: string;
  event?: { id: string; name: string; name_ar: string };
  name: string;
  name_ar: string;
  company_name?: string;
  company_name_ar?: string;
  description?: string;
  description_ar?: string;
  logo?: string;
  contact_person?: string;
  contact_email?: string;
  contact_phone?: string;
  website?: string;
  status: string;
  tier?: string;
  created_at: string;
}

export interface Rating {
  id: string;
  user_id: string;
  rateable_type: string;
  rateable_id: string;
  type: string | { value: string };
  overall_rating: number;
  comment: string;
  comment_ar: string;
  is_approved: boolean;
  created_at: string;
  rateable?: { id: string; name?: string; name_ar?: string };
}

export interface Banner {
  id: string;
  title: string;
  title_ar: string;
  image: string;
  image_ar?: string;
  image_url: string;
  url: string;
  link_url: string;
  description?: string;
  description_ar?: string;
  is_active: boolean;
  sort_order: number;
  position: string;
  start_date?: string | null;
  end_date?: string | null;
  starts_at?: string | null;
  ends_at?: string | null;
}

export interface Page {
  id: string;
  title: string;
  title_ar: string;
  slug: string;
  content: string;
  content_ar: string;
  type: string;
  is_active: boolean;
}

export interface FAQ {
  id: string;
  question: string;
  question_ar: string;
  answer: string;
  answer_ar: string;
  category: string;
  sort_order: number;
  is_active: boolean;
}

export interface Notification {
  id: string;
  title: string;
  title_ar: string;
  body: string;
  body_ar: string;
  type: string;
  read_at: string | null;
  created_at: string;
}

export interface RentalContract {
  id: string;
  contract_number: string;
  status: string;
  start_date: string;
  end_date: string;
  total_amount: number;
  monthly_rent: number;
  deposit_amount: number;
  created_at: string;
  tenant?: { id: string; name: string };
  space?: { id: string; name: string; name_ar: string };
}

export interface SupportTicket {
  id: string;
  subject: string;
  subject_ar: string;
  category: string;
  priority: string;
  status: string;
  created_at: string;
  user?: { id: string; name: string; email: string };
}

export interface Invoice {
  id: string;
  invoice_number: string;
  total_amount: number;
  paid_amount: number;
  status: string;
  due_date: string;
  created_at: string;
  user?: { id: string; name: string };
}

export interface Payment {
  id: string;
  amount: number;
  payment_method: string;
  transaction_id: string;
  status: string;
  card_last_four: string;
  card_brand: string;
  created_at: string;
  user?: { id: string; name: string };
}

export interface Settings {
  site_name: string;
  site_name_ar: string;
  contact_email: string;
  contact_phone: string;
  support_email: string;
  maintenance_mode: boolean;
  allow_registration: boolean;
  auto_approve_profiles: boolean;
  max_visit_requests_per_day: number;
  max_rental_requests_per_merchant: number;
  default_currency: string;
  timezone: string;
  cors_allowed_origins: string;
  payment_enabled: boolean;
  payment_gateway_mode: string;
  payment_default_currency: string;
  payment_3d_secure: boolean;
  sms_enabled: boolean;
  sms_default_channel: string;
  sms_max_attempts_per_hour: number;
  sms_code_length: number;
}
