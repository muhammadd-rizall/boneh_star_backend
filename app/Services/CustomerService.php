<?php

namespace App\Services;

use App\Repositories\CustomerRepository;

class CustomerService
{
    protected CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /***********************************************************
     * Admin
     */

    // get all customers
    public function getAllCustomers()
    {
        return $this->customerRepository->getAllCustomers();
    }

    // get paginated customers
    public function getPaginatedCustomers(int $perPage = 10, ?string $search = null)
    {
        return $this->customerRepository->getPaginatedCustomers($perPage, $search);
    }

    // get customer by id
    public function getCustomerById(string $id)
    {
        return $this->customerRepository->find($id);
    }

    // create customer
    public function createCustomer(array $data)
    {
        return $this->customerRepository->create($data);
    }

    // update customer by id
    public function updateCustomer(string $id, array $data)
    {
        return $this->customerRepository->update($id, $data);
    }

    // delete customer by id
    public function deleteCustomer(string $id)
    {
        return $this->customerRepository->delete($id);
    }

    // get all trashed customers
    public function getTrashedCustomers()
    {
        return $this->customerRepository->allTrashed();
    }

    // restore customer by id
    public function restoreCustomer(string $id)
    {
        return $this->customerRepository->restore($id);
    }

    // force delete customer by id
    public function forceDeleteCustomer(string $id)
    {
        return $this->customerRepository->forceDelete($id);
    }

    // get user statistics
    public function getStatistics(): array
    {
        return $this->customerRepository->getStatistics();
    }

    /**************************************************
     * Customer
     */

    // get statistic for custommer view
    public function getCustomerBookingStatistics(string $customerId): array
    {
        return $this->customerRepository->getCustomerBookingStatistics($customerId);
    }
}
