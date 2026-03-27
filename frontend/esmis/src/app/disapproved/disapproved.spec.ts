import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Disapproved } from './disapproved';

describe('Disapproved', () => {
  let component: Disapproved;
  let fixture: ComponentFixture<Disapproved>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Disapproved]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Disapproved);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
