<?php

return [

    // ================== AUTH ==================
    'auth' => [
        'token_required' => 'Token is required',
        'token_invalid' => 'Invalid token',
        'token_expired' => 'Token has expired',
        'account_inactive' => 'Account is not active',
        'role_required' => 'You do not have permission to access this resource',
        'permission_denied' => 'You do not have permission to perform this action',
        'unauthorized' => 'Unauthorized access',
    ],

    // ================== PROFILE ==================
    'profile' => [
        'not_found' => 'Business profile not found',
        'already_exists' => 'You already have a business profile',
        'created' => 'Business profile created successfully',
        'updated' => 'Business profile updated successfully',
        'cannot_be_modified' => 'Profile cannot be modified in this state',
        'required' => 'You must create a business profile first',
        'required_for_rental' => 'You must verify your profile to submit rental requests',
        'pending' => 'Your profile is under review',
        'approved' => 'Profile has been verified',
        'rejected' => 'Profile verification has been rejected',
        'not_pending' => 'Profile is not pending review',
    ],

    // ================== EVENT ==================
    'event' => [
        'not_found' => 'Event not found',
        'created' => 'Event created successfully',
        'updated' => 'Event updated successfully',
        'deleted' => 'Event deleted successfully',
        'has_active_requests' => 'Cannot delete event with active requests',
    ],

    // ================== SPACE ==================
    'space' => [
        'not_found' => 'Space not found',
        'created' => 'Space created successfully',
        'updated' => 'Space updated successfully',
        'deleted' => 'Space deleted successfully',
        'has_active_rentals' => 'Cannot delete space with active rentals',
    ],

    // ================== VISIT REQUEST ==================
    'visit_request' => [
        'not_found' => 'Visit request not found',
        'already_exists' => 'You already have a visit request for this date',
        'created' => 'Visit request created successfully',
        'updated' => 'Visit request updated successfully',
        'cancelled' => 'Visit request cancelled',
        'cannot_be_modified' => 'Request cannot be modified in this state',
        'cannot_be_cancelled' => 'Request cannot be cancelled in this state',
        'date_outside_event' => 'Visit date is outside event period',
        'event_not_available' => 'Event is not available for visits',
        'approved' => 'Visit request approved',
        'rejected' => 'Visit request rejected',
        'not_pending' => 'Request is not pending',
    ],

    // ================== RENTAL REQUEST ==================
    'rental_request' => [
        'not_found' => 'Rental request not found',
        'already_exists' => 'You already have a rental request for this space in this period',
        'created' => 'Rental request created successfully',
        'updated' => 'Rental request updated successfully',
        'cancelled' => 'Rental request cancelled',
        'cannot_be_modified' => 'Request cannot be modified in this state',
        'cannot_be_cancelled' => 'Request cannot be cancelled in this state',
        'dates_outside_event' => 'Rental dates are outside event period',
        'space_not_available' => 'Space is not available for this period',
        'approved' => 'Rental request approved',
        'rejected' => 'Rental request rejected',
        'not_pending' => 'Request is not pending',
        'must_be_approved' => 'Request must be approved',
        'payment_recorded' => 'Payment recorded successfully',
    ],

    // ================== FAVORITE ==================
    'favorite' => [
        'added' => 'Added to favorites',
        'removed' => 'Removed from favorites',
    ],

    // ================== NOTIFICATION ==================
    'notification' => [
        'marked_as_read' => 'Notification marked as read',
        'all_marked_as_read' => 'All notifications marked as read',
    ],

    // ================== VALIDATION ==================
    'validation' => [
        'company_name_required' => 'Company name is required',
        'contact_phone_required' => 'Contact phone is required',
        'business_type_required' => 'Business type is required',
        'must_be_image' => 'File must be an image',
        'file_too_large' => 'File is too large',
        'event_required' => 'Event is required',
        'visit_date_required' => 'Visit date is required',
        'visit_date_future' => 'Visit date must be in the future',
        'visitors_count_required' => 'Visitors count is required',
        'visitors_count_min' => 'Visitors count must be at least 1',
        'visitors_count_max' => 'Visitors count exceeds the maximum allowed',
        'space_required' => 'Space is required',
        'start_date_required' => 'Start date is required',
        'start_date_future' => 'Start date must be in the future',
        'end_date_required' => 'End date is required',
        'end_date_after_start' => 'End date must be after start date',
        'type_required' => 'Type is required',
        'invalid_type' => 'Invalid type',
        'id_required' => 'ID is required',
        'name_required' => 'Name is required',
        'name_ar_required' => 'Arabic name is required',
        'category_required' => 'Category is required',
        'city_required' => 'City is required',
        'address_required' => 'Address is required',
        'location_code_required' => 'Location code is required',
        'location_code_unique' => 'Location code is already in use',
        'area_required' => 'Area is required',
        'price_required' => 'Price is required',
        'rejection_reason_required' => 'Rejection reason is required',
    ],

    // ================== GENERAL ==================
    'resource_not_found' => 'Resource not found',
    'forbidden' => 'This action is not allowed',
    'unauthorized' => 'Unauthorized',
    'validation_failed' => 'Validation failed',
    'server_error' => 'Server error',
];
