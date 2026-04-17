-- Remove unique constraint to allow multiple attendance records per day
-- This enables employees to check in/out multiple times at same or different branches

ALTER TABLE attendance DROP INDEX unique_attendance;
