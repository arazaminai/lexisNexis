import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DetailsDialogueComponent } from '../document-list/details/details-dialogue.component';

describe('DocumentDetailsComponent', () => {
  let component: DetailsDialogueComponent;
  let fixture: ComponentFixture<DetailsDialogueComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [DetailsDialogueComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DetailsDialogueComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
