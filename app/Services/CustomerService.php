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

    // get all customers
    public function getAllCustomers()
    {
        return $this->customerRepository->getAllCustomers();
    }

    // get paginated customers
    public function getPaginatedCustomers(int $perPage = 10)
    {
        return $this->customerRepository->getPaginatedCustomers($perPage);
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
}
