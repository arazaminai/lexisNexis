import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import axios from 'axios';

@Component({
  selector: 'app-document-list',
  // standalone: true,
  imports: [CommonModule],
  templateUrl: './document-list.component.html',
  styleUrl: './document-list.component.scss'
})
export class DocumentListComponent {
  @Input() documents: any[] = [];

  viewDocument(id: number) {
    // Implement view logic, e.g., open in a new tab
    window.open(`http://localhost:8080/api/documents/?id=${id}`, '_blank');
  }

  downloadDocument($filepath: string) {
    // Implement download logic
    window.location.href = `http://localhost:8080${$filepath}`;
  }

  deleteDocument(id: number) {
    // Emit an event or call a service to delete the document
    axios.delete(`http://localhost:8080/api/documents/?id=${id}`)
      .then(() => {
        this.documents = this.documents.filter(doc => doc.id !== id);
      })
      .catch(err => console.error(err));
    console.log(`Delete document with ID: ${id}`);
  }
}